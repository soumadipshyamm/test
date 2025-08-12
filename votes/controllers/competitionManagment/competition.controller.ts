import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../constants/message";
import CompetitionModel from "../../../../model/competition.model";
import cron from 'node-cron';
import { getDetailsByEmail } from "../../../../utils/helper";
import VotePackageModel from "../../../../model/votePackage.model";
import { formatCompetitionResponse, formatCompetitionsResponse, formatAllCompetitionsResponse } from "../../../../utils/formatter/competitionResponseFormatter";
import SavedCompetitionModel from "../../../../model/savedCompetition.model";
import VoteModel from "../../../../model/vote.model";
import ParticipantModel from "../../../../model/participant.model";
import { PARTICIPATION_STATUS, COMPETITION_STATUS, MEMBER_STATUS } from "../../../../constants/status/status";
import VotingSubscriptionModel from "../../../../model/votingSubscription.model";
import transactionModel from "../../../../model/transaction.model";
import { roles, ROLES } from "../../../../constants/roles/roles";
import GroupOwnerModel from "../../../../model/groupOwner.model";
import MemberModel from "../../../../model/member.model";
import { cp } from "fs";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";
dayjs.extend(duration);


export const addCompetition = async (req: Request, res: Response): Promise<any> => {
	try {
		let user = await getDetailsByEmail(req.user.email);
		req.body['created_by'] = user?._id
		const competitionInstance = await CompetitionModel.create(req.body);
		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: competitionInstance
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};
export const listCompetitions = async (req: Request, res: Response): Promise<any> => {
	try {
		const competitions = await CompetitionModel.find()
			.populate('competition_type')
			.populate('created_by')
			.populate('creator_company')
			.populate({
				path: 'rounds.additional_vote_package'
			})
			.sort({ createdAt: -1 }).lean();
		const userDetails = await getDetailsByEmail(req.user.email);

		if (!userDetails) {
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: 'User not authenticated.'
			});
		}
		const competitionDetails = await formatCompetitionsResponse(competitions, userDetails);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitionDetails
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
};
export const getCompetitionDetails = async (req: Request, res: Response): Promise<void> => {
	try {
		const { id } = req.params;
		const userDetails = await getDetailsByEmail(req.user.email);

		// Fetch competition
		const competition = await CompetitionModel.findById(id)
			.populate('competition_type')
			.populate('created_by')
			.populate('creator_company')
			.populate({ path: 'rounds.additional_vote_package' })
			.lean();

		if (!competition) {
			res.status(StatusCodes.NOT_FOUND).json({ message: "Competition not found" });
			return;
		}

		// Get user
		if (!userDetails) {
			res.status(StatusCodes.UNAUTHORIZED).json({ message: "User not authenticated" });
			return;
		}

		// Determine current or upcoming round
		const now = dayjs();
		const sortedRounds = (competition.rounds || []).sort((a, b) =>
			dayjs(a.start_date_time).diff(dayjs(b.start_date_time))
		);

		let roundStatus: "ongoing" | "upcoming" | "completed" = "completed";
		let currentRound = sortedRounds.find(r =>
			dayjs(r.start_date_time).isBefore(now) && dayjs(r.end_date_time).isAfter(now)
		);

		if (!currentRound) {
			currentRound = sortedRounds.find(r => dayjs(r.start_date_time).isAfter(now));
			if (currentRound) roundStatus = "upcoming";
		} else {
			roundStatus = "ongoing";
		}

		// Time status
		let time_status = null;
		if (roundStatus === "ongoing" && currentRound) {
			const ms = dayjs(currentRound.end_date_time).diff(now);
			const dur = dayjs.duration(ms);
			time_status = `Ended in ${dur.days()}d ${dur.hours()}h ${dur.minutes()}m`;
		}
		if (roundStatus === "upcoming" && currentRound) {
			const ms = dayjs(currentRound.start_date_time).diff(now);
			const dur = dayjs.duration(ms);
			time_status = `Starts in ${dur.days()}d ${dur.hours()}h ${dur.minutes()}m`;
		}

		// Format competition response
		const competitionDetails: any = await formatCompetitionResponse(competition, userDetails);
		competitionDetails.current_round = currentRound?.round_no ?? null;
		competitionDetails.round_status = roundStatus;
		competitionDetails.time_status = time_status;
		competitionDetails.current_round_details = currentRound || null;

		// Fetch participant data
		const participants = await ParticipantModel.find({
			competition_object_id: id,
			participant_object_id: userDetails._id.toString(),
			status: { $in: ['PARTICIPATING'] },
			// round_no: currentRound?.round_no,
			round_object_id: currentRound?._id,
			participant_payment_status: "SUCCESS"
		}).populate([{ path: 'transaction_object_id' }, { path: 'round_object_id' }])
			.lean();

		if (userDetails.role === ROLES.competition_creator) {
			//also add revenue generated by the competition each round with the participant details only seen the competition creator
			// Fetch voting subscriptions for the competition
			const votingSubscriptions = await VotingSubscriptionModel.aggregate([
				{
					$match: {
						competition_object_id: id,
						member_object_id: userDetails._id,
						paymentStatus: 'PAID'
					}
				},
				{
					$group: {
						_id: null,
						total_cost: { $sum: '$votes_price' }
					}
				}
			]);
			const totalCost = votingSubscriptions.length > 0 ? votingSubscriptions[0].total_cost : 0;
			// Add total cost to competition details
			competitionDetails.total_cost = totalCost;
			// Add round number to each participant			
		}

		// Add participant details to competition response
		competitionDetails.participantCount = participants.length;
		competitionDetails.participantCompetition = participants.length > 0;
		competitionDetails.participantRoundStatus = participants.length > 0 ? participants[0].status : null;
		competitionDetails.participantRoundNo = participants.length > 0 ? participants[0].round_no : null;
		competitionDetails.participantRoundPaymentStatus = participants.length > 0 ? participants[0].participant_payment_status : null;
		competitionDetails.participantDetails = participants;
		competitionDetails.enrolled_competition = participants.length > 0 ? (participants[0].participant_payment_status == "SUCCESS" ? true : false) : false;

		res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitionDetails
		});
	} catch (error) {
		console.error("Error in getCompetitionDetails:", error);
		res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
};
export const updateCompetition = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const updatedCompetition = await CompetitionModel.findByIdAndUpdate(id, req.body, { new: true, runValidators: true });

		if (!updatedCompetition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found",
			});
		}

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: updatedCompetition
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.put.fail,
			error
		});
	}
};
export const deleteCompetition = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const deletedCompetition = await CompetitionModel.findByIdAndDelete(id);

		if (!deletedCompetition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found",
			});
		}

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.delete.succ
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.delete.fail,
			error
		});
	}
};
export const competitionAcceptReject = async (req: Request, res: Response): Promise<any> => {
	const { status, _id, remarks } = req.body;

	const competition = await CompetitionModel.findById(_id)
	if (competition && competition.status == 'REJECTED') {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: "Competition already rejected",
		});
	}

	try {
		const updatedCompetition = await CompetitionModel.findByIdAndUpdate(
			_id,
			{ status, remarks },
			{ new: true, runValidators: true }
		);

		if (!updatedCompetition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found",
			});
		}

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: updatedCompetition
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.put.fail,
			error
		});
	};
};
export const competitionCreatorAcceptRejectByAdmin = async (req: Request, res: Response): Promise<any> => {
	const { status, _id, remarks } = req.body;

	const competitionCreator = await MemberModel.findById(_id)
	if (competitionCreator && competitionCreator.is_approved == MEMBER_STATUS.approved) {
		return res.status(StatusCodes.ACCEPTED).json({
			message: "Competition creator already Approved",
		});
	}
	try {
		const updatedCompetitionCreator = await MemberModel.findByIdAndUpdate(
			_id,
			{
				is_approved: status, remarks
			},
			{ new: true, runValidators: true }
		);
		if (!updatedCompetitionCreator) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found",
			});
		}
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: updatedCompetitionCreator
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.put.fail,
			error
		});
	};
};
export const competitionCreatorAcceptRejectList = async (req: Request, res: Response): Promise<any> => {
	const { status } = req.params;
	let data: any = {};
	let competitionCreatorList: any = [];
	if (!status) {
		competitionCreatorList = await MemberModel.find({ role: roles.competition_creator, is_registered: true, is_verified: true }).lean();
	} else {
		switch (status) {
			case "pending":
				data = MEMBER_STATUS.pending;
				break;
			case "approved":
				data = MEMBER_STATUS.approved;
				break;
			case "rejected":
				data = MEMBER_STATUS.rejected;
				break;
		}
		competitionCreatorList = await MemberModel.find({ role: roles.competition_creator, is_approved: data, is_registered: true, is_verified: true }).lean();
	}
	if (!competitionCreatorList || competitionCreatorList.length === 0) {
		return res.status(StatusCodes.NOT_FOUND).json({
			message: "No competition creators found",
		});
	}
	return res.status(StatusCodes.OK).json({
		message: MESSAGE.put.succ,
		Result: competitionCreatorList
	});
}

export const filterCompetitions = async (req: Request, res: Response): Promise<any> => {
	try {
		const { status, name } = req.body;
		const filter: any = {};
		if (status) {
			filter.status = status;
		}

		if (name) {
			filter.name = { $regex: new RegExp(name as string, 'i') };
		}

		const competitions = await CompetitionModel.find(filter)
			.populate('competition_type')
			.populate('created_by')
			.populate('creator_company')
			.populate({
				path: 'rounds.additional_vote_package'
			}).sort({ createdAt: -1 }).lean();
		const userDetails = await getDetailsByEmail(req.user.email);

		if (!userDetails) {
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: 'User not authenticated.'
			});
		}
		const competitionDetails = await formatAllCompetitionsResponse(competitions, userDetails);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitionDetails
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
};
export const getVotingHistory = async (req: Request, res: Response): Promise<any> => {
	try {
		const user = await getDetailsByEmail(req.user.email);
		if (!user) {
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: 'User not authenticated.'
			});
		}
		const voterId = user._id;

		const votingHistory = await VoteModel.aggregate([
			{ $match: { voter_object_id: voterId } },
			{
				$lookup: {
					from: 'competitions',
					localField: 'competition_object_id',
					foreignField: '_id',
					as: 'competitionDetails'
				}
			},
			{ $unwind: '$competitionDetails' },
			{
				$lookup: {
					from: 'members',
					localField: 'participant_object_id',
					foreignField: '_id',
					as: 'participantDetails'
				}
			},
			{ $unwind: '$participantDetails' },
			{
				$lookup: {
					from: 'participants',
					let: {
						competitionId: '$competition_object_id',
						participantId: '$participant_object_id'
					},
					pipeline: [
						{
							$match: {
								$expr: {
									$and: [
										{ $eq: ['$competition_object_id', '$$competitionId'] },
										{ $eq: ['$participant_object_id', '$$participantId'] }
									]
								}
							}
						},
						{
							$sort: { createdAt: -1 }
						},
						{
							$limit: 1
						},
						{
							$project: {
								status: 1
							}
						}
					],
					as: 'participantStatus'
				}
			},

			{ $unwind: { path: '$participantStatus', preserveNullAndEmptyArrays: true } },
			{ $unwind: '$competitionDetails.rounds' },
			{
				$match: {
					$expr: { $eq: ['$round_object_id', '$competitionDetails.rounds._id'] }
				}
			},

			{
				$lookup: {
					from: 'voting_subscriptions',
					let: {
						compId: '$competition_object_id',
						roundId: '$round_object_id',
						participantId: '$participant_object_id',
						userId: '$voter_object_id'
					},
					pipeline: [
						{
							$match: {
								$expr: {
									$and: [
										{ $eq: ['$competition_object_id', '$$compId'] },
										{ $eq: ['$round_object_id', '$$roundId'] },
										{ $eq: ['$participant_object_id', '$$participantId'] },
										{ $eq: ['$member_object_id', '$$userId'] },
										{ $eq: ['$paymentStatus', 'PAID'] }
									]
								}
							}
						},
						{
							$group: {
								_id: null,
								total_cost: { $sum: '$votes_price' }
							}
						}
					],
					as: 'subscriptionInfo'
				}
			},

			{
				$addFields: {
					cost: {
						$ifNull: [
							{ $arrayElemAt: ['$subscriptionInfo.total_cost', 0] },
							0
						]
					}
				}
			},

			{
				$group: {
					_id: {
						competitionId: '$competition_object_id',
						participantId: '$participant_object_id',
						roundId: '$round_object_id'
					},
					competitionName: { $first: '$competitionDetails.name' },
					competitionId: { $first: '$competition_object_id' },
					participantName: {
						$first: {
							$concat: [
								'$participantDetails.first_name',
								' ',
								'$participantDetails.last_name'
							]
						}
					},
					status: { $first: '$participantStatus.status' }, // Capture status here
					roundId: { $first: '$round_object_id' },
					roundNo: { $first: '$competitionDetails.rounds.round_no' },
					roundVotes: { $sum: 1 },
					totalCost: { $first: '$cost' }
				}
			},

			{
				$group: {
					_id: {
						competitionId: '$competitionId',
						participantName: '$participantName'
					},
					competitionId: { $first: '$competitionId' },
					competitionName: { $first: '$competitionName' },
					participantName: { $first: '$participantName' },
					status: { $first: '$status' },
					votingCount: { $sum: '$roundVotes' },
					round: {
						$push: {
							round_id: '$roundId',
							round_no: '$roundNo',
							total_votes: '$roundVotes',
							total_cost: '$totalCost'
						}
					}
				}
			},

			{
				$project: {
					_id: 0,
					competitionId: 1,
					competitionName: 1,
					participantName: 1,
					status: 1,
					votingCount: 1,
					round: 1
				}
			}
		]);

		return res.status(StatusCodes.OK).json({
			message: 'Voting history retrieved successfully',
			data: votingHistory
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
};
export const dashboardReport = async (req: Request, res: Response): Promise<any> => {
	try {
		const user = await getDetailsByEmail(req.user.email)
		if (!user) {
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: 'User not authenticated.'
			});
		}
		const totalVotingRevenueGenerated = await votingRevenueGenerated();
		const totalParticipantRevenueGenerated = await participantRevenueGenerated();
		const totalActiveCompetitions = await activeCompetitions(user);
		const totalPendingCompetitions = await pendingCompetitions(user);
		const totalRejectedCompetitions = await rejectedCompetitions(user);
		const totalCompletedCompetitions = await completedCompetitions(user);
		const totalCompetitions = await allCompetitions(user);
		const totalActiveParticipants = await activeParticipants(user);
		const totalVotesReceived = await votesReceived(user);
		const totalAdmin = await countUsersByRole(ROLES.admin)
		const totalCompetitionCreator = await countUsersByRole(ROLES.competition_creator)
		const totalParticipant = await countUsersByRole(ROLES.participant)
		const totalVoter = await countUsersByRole(ROLES.individual_voter)

		let data = {};
		if (req.user.role === ROLES.super_admin || req.user.role === ROLES.admin) {
			data = {
				"votingRevenueGenerated": totalVotingRevenueGenerated,
				"participantRevenueGenerated": totalParticipantRevenueGenerated,
				"activeCompetitions": totalActiveCompetitions,
				"activeParticipants": totalActiveParticipants,
				"totalAdmin": totalAdmin,
				"totalCompetitionCreator": totalCompetitionCreator,
				"totalParticipant": totalParticipant,
				"totalVoter": totalVoter
			}
		} else if (req.user.role === ROLES.competition_creator) {
			data = {
				"allCompetitions": totalCompetitions,
				"activeCompetitions": totalActiveCompetitions,
				"pendingCompetition": totalPendingCompetitions,
				"rejectedCompetition": totalRejectedCompetitions,
				"completedCompetition": totalCompletedCompetitions,
				"activeParticipants": totalActiveParticipants,
				"totalVotesReceived": totalVotesReceived,
			}
		} else if (req.user.role === ROLES.participant) {
			const totalCompetitionWon = await wonnedCompetition(user)
			const totalParticipatedCompetition = await participatedCompetition(user)
			data = {
				"activeCompetitions": totalActiveCompetitions,
				"totalCompetitionWon": totalCompetitionWon,
				"totalParticipatedCompetition": totalParticipatedCompetition,
				"totalVotesReceived": totalVotesReceived,
			}
		}

		return res.status(StatusCodes.OK).json({
			message: 'Data retrived successfully',
			data: data
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
};
export const currentRound = async (req: Request, res: Response): Promise<any> => {
	try {
		const competitionId = req.query.competitionId || req.params.competitionId;
		const competition = await CompetitionModel.findById(competitionId);
		// const competition = await CompetitionModel.findById("6864d6a4f450a36e6f51a53d");

		if (!competition || !competition.rounds || competition.rounds.length === 0) {
			return res.status(404).json({ message: "No rounds found" });
		}
		const now = new Date();

		// Try to find ongoing round
		let round = competition.rounds.find(r => {
			const start = new Date(r.start_date_time);
			const end = new Date(r.end_date_time);
			return start <= now && now <= end;
		});
		const
			current_rounds = round?.round_no;

		if (round) {
			return res.status(200).json({
				status: "ongoing",
				time_status: "ongoing now",
				result: { current_rounds, round }
			});
		}

		// If no ongoing, find nearest upcoming
		const upcomingRounds = competition.rounds
			.filter(r => new Date(r.start_date_time) > now)
			.sort((a, b) => new Date(a.start_date_time).getTime() - new Date(b.start_date_time).getTime());

		if (upcomingRounds.length === 0) {
			return res.status(404).json({ message: "No upcoming rounds available" });
		}

		const upcoming = upcomingRounds[0];
		const timeUntil = dayjs(upcoming.start_date_time).diff(dayjs(), 'millisecond');
		const durationObj = dayjs.duration(timeUntil);

		return res.status(200).json({
			status: "upcoming",
			time_status: `Starts in ${durationObj.days()}d ${durationObj.hours()}h ${durationObj.minutes()}m`,
			round: upcoming
		});
	} catch (error) {
		console.error("Error determining current/upcoming round:", error);
		return res.status(500).json({ message: "Internal server error" });
	}
};

// *************************************************************************************
// export const getVotingHistory = async (req: Request, res: Response): Promise<any> => {
// 	try {
// 		const user = await getDetailsByEmail(req.user.email);
// 		if (!user) {
// 			return res.status(StatusCodes.UNAUTHORIZED).json({
// 				message: 'User not authenticated.'
// 			});
// 		}
// 		const voterId = user._id;

// 		const votingHistory = await VoteModel.aggregate([
// 			{ $match: { voter_object_id: voterId } },
// 			{
// 				$lookup: {
// 					from: 'competitions',
// 					localField: 'competition_object_id',
// 					foreignField: '_id',
// 					as: 'competitionDetails'
// 				}
// 			},
// 			{ $unwind: '$competitionDetails' },
// 			{
// 				$lookup: {
// 					from: 'members',
// 					localField: 'participant_object_id',
// 					foreignField: '_id',
// 					as: 'participantDetails'
// 				}
// 			},
// 			{ $unwind: '$participantDetails' },

// 			// Expand rounds
// 			{ $unwind: '$competitionDetails.rounds' },
// 			{
// 				$match: {
// 					$expr: { $eq: ['$round_object_id', '$competitionDetails.rounds._id'] }
// 				}
// 			},

// 			{
// 				$lookup: {
// 					from: 'voting_subscriptions',
// 					let: {
// 						compId: '$competition_object_id',
// 						roundId: '$round_object_id',
// 						participantId: '$participant_object_id',
// 						userId: '$voter_object_id'
// 					},
// 					pipeline: [
// 						{
// 							$match: {
// 								$expr: {
// 									$and: [
// 										{ $eq: ['$competition_object_id', '$$compId'] },
// 										{ $eq: ['$round_object_id', '$$roundId'] },
// 										{ $eq: ['$participant_object_id', '$$participantId'] },
// 										{ $eq: ['$member_object_id', '$$userId'] },
// 										{ $eq: ['$paymentStatus', 'PAID'] }
// 									]
// 								}
// 							}
// 						},
// 						{
// 							$group: {
// 								_id: null,
// 								total_cost: { $sum: '$votes_price' }
// 							}
// 						}
// 					],
// 					as: 'subscriptionInfo'
// 				}
// 			},

// 			{
// 				$addFields: {
// 					cost: {
// 						$ifNull: [
// 							{ $arrayElemAt: ['$subscriptionInfo.total_cost', 0] },
// 							0
// 						]
// 					}
// 				}
// 			},

// 			{
// 				$group: {
// 					_id: {
// 						competitionId: '$competition_object_id',
// 						participantId: '$participant_object_id',
// 						roundId: '$round_object_id'
// 					},
// 					competitionName: { $first: '$competitionDetails.name' },
// 					competitionId: { $first: '$competition_object_id' },
// 					participantName: {
// 						$first: {
// 							$concat: [
// 								'$participantDetails.first_name',
// 								' ',
// 								'$participantDetails.last_name'
// 							]
// 						}
// 					},
// 					roundId: { $first: '$round_object_id' },
// 					roundNo: { $first: '$competitionDetails.rounds.round_no' },
// 					roundVotes: { $sum: 1 },
// 					totalCost: { $first: '$cost' }
// 				}
// 			},

// 			{
// 				$group: {
// 					_id: {
// 						competitionId: '$competitionId',
// 						participantName: '$participantName'
// 					},
// 					competitionId: { $first: '$competitionId' },
// 					competitionName: { $first: '$competitionName' },
// 					participantName: { $first: '$participantName' },
// 					votingCount: { $sum: '$roundVotes' },
// 					round: {
// 						$push: {
// 							round_id: '$roundId',
// 							round_no: '$roundNo',
// 							total_votes: '$roundVotes',
// 							total_cost: '$totalCost'
// 						}
// 					}
// 				}
// 			},

// 			{
// 				$project: {
// 					_id: 0,
// 					competitionId: 1,
// 					competitionName: 1,
// 					participantName: 1,
// 					votingCount: 1,
// 					round: 1
// 				}
// 			}
// 		]);
// 		return res.status(StatusCodes.OK).json({
// 			message: 'Voting history retrieved successfully',
// 			data: votingHistory
// 		});
// 	} catch (error) {
// 		console.error(error);
// 		return res.status(StatusCodes.BAD_REQUEST).json({
// 			message: MESSAGE.get.fail,
// 			error
// 		});
// 	}
// };


///============================CRON ==========================///

// cron.schedule('0 * * * *', async () => {

cron.schedule('*/10 * * * * *', async () => {
	competitionStatusUpdate()
	participantMoveToNextRound()
});

const competitionStatusUpdate = async (): Promise<any> => {
	try {
		const currentDate = new Date();

		const competitionsToUpdate = await CompetitionModel.find({
			status: 'ACTIVE',
			rounds: { $exists: true, $not: { $size: 0 } },
		}).populate('rounds').then(comp =>
			comp.filter(competition => {
				const lastRound = competition.rounds[competition.rounds.length - 1];
				return lastRound && lastRound.end_date_time < currentDate;
			})
		);

		if (competitionsToUpdate.length > 0) {
			await Promise.all(competitionsToUpdate.map(async (competition) => {
				competition.status = COMPETITION_STATUS.completed;
				await competition.save();
				console.log(`Competition ${competition.name} status updated to completed.`);
			}));
		}
	} catch (error) {
		console.error('Error updating competitions:', error);
	}
};

const participantMoveToNextRound = async (): Promise<any> => {
	try {
		const competitions = await CompetitionModel.find({
			status: COMPETITION_STATUS.active
		});

		for (const competition of competitions) {
			for (const round of competition.rounds) {
				const currentDateUTC = new Date();
				const currentDate = new Date(currentDateUTC.getTime() + (5.5 * 60 * 60 * 1000));
				if (new Date(round.end_date_time) < currentDate) {
					const votes = await ParticipantModel.aggregate([
						{
							$match: {
								competition_object_id: competition._id,
								round_object_id: round._id
							}
						},
						{
							$lookup: {
								from: 'votes',
								localField: 'participant_object_id',
								foreignField: 'participant_object_id',
								as: 'votes'
							}
						},
						{
							$unwind: {
								path: '$votes',
								preserveNullAndEmptyArrays: true
							}
						},
						{
							$group: {
								_id: '$participant_object_id',
								voteCount: { $sum: { $cond: [{ $ifNull: ['$votes', false] }, 1, 0] } },
								participantName: { $first: '$participant_name' },
							}
						}
					]);

					const totalParticipantsLastRound = await ParticipantModel.countDocuments({ round_object_id: round._id });
					const numberToProceed = Math.floor((totalParticipantsLastRound * round.no_of_participant_proceeding) / 100);

					const voteCounts: { [key: string]: { count: number, name: string } } = {};
					votes.forEach(vote => {
						voteCounts[vote._id.toString()] = { count: vote.voteCount, name: vote.participantName };
					});

					let participantsToProceed = [];
					if (totalParticipantsLastRound <= competition.no_of_winner) {
						participantsToProceed = Object.keys(voteCounts);
					} else {
						const sortedParticipants = Object.entries(voteCounts)
							.sort((a, b) => b[1].count - a[1].count)
							.slice(0, numberToProceed)
							.map(entry => entry[0]);

						if (sortedParticipants.length >= competition.no_of_winner) {
							participantsToProceed = sortedParticipants;
						} else {
							const sortedAllParticipants = Object.entries(voteCounts)
								.sort((a, b) => b[1].count - a[1].count)
								.map(entry => entry[0]);
							participantsToProceed = sortedAllParticipants.slice(0, competition.no_of_winner);
						}
					}

					const nextRoundId = competition.rounds[competition.rounds.indexOf(round) + 1]?._id;

					for (const participantId of Object.keys(voteCounts)) {
						const existingParticipant = await ParticipantModel.findOne({
							competition_object_id: competition._id,
							participant_object_id: participantId,
							round_object_id: round._id
						});
						if (existingParticipant) {
							if (participantsToProceed.includes(participantId)) {
								existingParticipant.status = PARTICIPATION_STATUS.participated;
								if (nextRoundId) {
									const nextParticipantionEntry = await ParticipantModel.findOne({
										competition_object_id: competition._id,
										participant_object_id: participantId,
										round_object_id: nextRoundId
									});
									if (!nextParticipantionEntry) {
										// Safely find the participant's vote
										const participate = votes.find(vote => vote._id.toString() === participantId);
										if (participate) {
											const participantData = voteCounts[participantId];
											const newParticipant = new ParticipantModel({
												competition_object_id: competition._id,
												participant_object_id: participantId,
												round_object_id: nextRoundId,
												competition_name: competition.name,
												round_no: round.round_no + 1,
												participant_name: participantData.name,
												status: PARTICIPATION_STATUS.participating
											});
											await newParticipant.save();
										}
									}
								}
							} else {
								existingParticipant.status = PARTICIPATION_STATUS.eliminated;
							}
							await existingParticipant.save();
						}
					}

					// Handle last round winners
					if (!nextRoundId) {
						const sortedWinners = Object.entries(voteCounts)
							.sort((a, b) => b[1].count - a[1].count)
							.slice(0, competition.no_of_winner)
							.map(entry => entry[0]);

						for (const participantId of Object.keys(voteCounts)) {
							const existingParticipant = await ParticipantModel.findOne({
								competition_object_id: competition._id,
								participant_object_id: participantId,
								round_object_id: round._id
							});
							if (existingParticipant) {
								if (sortedWinners.includes(participantId)) {
									existingParticipant.status = PARTICIPATION_STATUS.winner;
								} else {
									existingParticipant.status = PARTICIPATION_STATUS.eliminated;
								}

								await existingParticipant.save();
							}
						}
					}
				}
			}
		}
	} catch (error) {
		console.error('Error processing rounds:', error);
	}
};

const votingRevenueGenerated = async (): Promise<any> => {
	try {
		const result = await VotingSubscriptionModel.aggregate([
			{
				$group: {
					_id: null,
					totalVotesPrice: { $sum: "$votes_price" }
				}
			}
		]);

		return result.length > 0 ? result[0].totalVotesPrice : 0;
	} catch (error) {
		console.error('Error calculating total votes price:', error);
		throw error;
	}
};
const participantRevenueGenerated = async (): Promise<any> => {
	try {
		const result = await transactionModel.aggregate([
			{
				$group: {
					_id: null,
					totalAmount: { $sum: "$amount" }
				}
			}
		]);

		return result.length > 0 ? result[0].totalAmount : 0;
	} catch (error) {
		console.error('Error calculating total transaction price:', error);
		throw error;
	}
};
const allCompetitions = async (user: any): Promise<any> => {
	try {
		let competitions = 0;
		if (user.role == ROLES.competition_creator) {
			competitions = await CompetitionModel.countDocuments({ created_by: user._id });
		} else {
			competitions = await CompetitionModel.countDocuments({});
		}
		return competitions;
	} catch (error) {
		console.error('Error counting:', error);
		throw error;
	}
};
const activeCompetitions = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role == ROLES.competition_creator) {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.active, created_by: user._id });
		} else if (user.role === ROLES.participant) {
			const participantRecords = await ParticipantModel.find({
				participant_object_id: user._id,
				status: PARTICIPATION_STATUS.participating
			}).populate('competition_object_id')

			const competitionIds = participantRecords.map(participant => participant.competition_object_id._id);

			activeCount = await CompetitionModel.countDocuments({
				_id: { $in: competitionIds },
				status: COMPETITION_STATUS.active
			});
		} else {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.active });
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting active competitions:', error);
		throw error;
	}
};
const participatedCompetition = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role === ROLES.participant) {
			const participantRecords = await ParticipantModel.find({
				participant_object_id: user._id,
				status: PARTICIPATION_STATUS.participating
			}).populate('competition_object_id')

			const competitionIds = participantRecords.map(participant => participant.competition_object_id._id);

			activeCount = await CompetitionModel.countDocuments({
				_id: { $in: competitionIds }
			});
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting active competitions:', error);
		throw error;
	}
};
const wonnedCompetition = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role === ROLES.participant) {
			activeCount = await ParticipantModel.countDocuments({
				participant_object_id: user._id,
				status: PARTICIPATION_STATUS.winner
			});
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting active competitions:', error);
		throw error;
	}
};
const pendingCompetitions = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role == ROLES.competition_creator) {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.pending, created_by: user._id });
		} else {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.active });
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting:', error);
		throw error;
	}
};
const rejectedCompetitions = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role == ROLES.competition_creator) {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.rejected, created_by: user._id });
		} else {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.active });
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting:', error);
		throw error;
	}
};
const completedCompetitions = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role == ROLES.competition_creator) {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.completed, created_by: user._id });
		} else {
			activeCount = await CompetitionModel.countDocuments({ status: COMPETITION_STATUS.active });
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting:', error);
		throw error;
	}
};
const activeParticipants = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role == ROLES.competition_creator) {
			const competitions = await CompetitionModel.find({ created_by: user._id });
			if (competitions.length > 0) {
				const competitionIds = competitions.map(comp => comp._id);
				activeCount = await ParticipantModel.countDocuments({
					status: PARTICIPATION_STATUS.participating,
					competition_object_id: { $in: competitionIds }
				});
			}
		} else {
			activeCount = await ParticipantModel.countDocuments({ status: PARTICIPATION_STATUS.participating });
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting:', error);
		throw error;
	}
};
const votesReceived = async (user: any): Promise<any> => {
	try {
		let activeCount = 0;
		if (user.role == ROLES.competition_creator) {
			const competitions = await CompetitionModel.find({ created_by: user._id });
			if (competitions.length > 0) {
				const competitionIds = competitions.map(comp => comp._id);
				activeCount = await VoteModel.countDocuments({
					competition_object_id: { $in: competitionIds }
				});
			}
		} else if (user.role === ROLES.participant) {
			activeCount = await VoteModel.countDocuments({
				participant_object_id: user._id
			});
		} else {
			activeCount = await VoteModel.countDocuments({});
		}
		return activeCount;
	} catch (error) {
		console.error('Error counting:', error);
		throw error;
	}
};

const countUsersByRole = async (role: string): Promise<number> => {
	try {
		let count;

		if (role === ROLES.admin || role === ROLES.super_admin) {
			count = await GroupOwnerModel.countDocuments({ is_disabled: false, role: role });
		} else {
			count = await MemberModel.countDocuments({ role: role });
		}

		return count;
	} catch (error) {
		console.error('Error counting users by role:', error);
		throw error;
	}
};

// *****************************************************************************
