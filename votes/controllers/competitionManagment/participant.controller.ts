import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../constants/message";
import { currentRound, get_round_status, getDetailsByEmail, getRoundStatus, getTimeStatus } from "../../../../utils/helper";
import ParticipantModel from "../../../../model/participant.model";
import CompetitionModel from "../../../../model/competition.model";
import SavedCompetitionModel from "../../../../model/savedCompetition.model";
import moment from 'moment';
import { uploadImageToS3Service } from "../../../../services/uploadImageS3/UploadImageS3";
import VoteModel from "../../../../model/vote.model";
import VotingSubscriptionModel from "../../../../model/votingSubscription.model";
import mongoose from "mongoose";
import { PARTICIPATION_STATUS, TRANSACTION_STATUS } from "../../../../constants/status/status";
import { createStripePaymentIntents, verifyPaymentViaStripe } from "../../../../services/payment/stripe.service";
import { transactionStatusUpdate } from "../../../../services/transaction/transaction.service";
import dayjs from "dayjs";
import { log } from "node:console";
import VotePackageModel from "../../../../model/votePackage.model";
export const addParticipant = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_object_id, round_id } = req.body
		if (!competition_object_id) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition ID required."
			});
		}
		let user = await getDetailsByEmail(req.user.email);
		const competition = await CompetitionModel.findById(competition_object_id).select('name rounds');
		console.log(competition);
		if (!competition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found."
			});
		}
		if (!user?._id) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "User not found."
			});
		}
		// console.log("Competition Name------------------------------:", competition);
		const round = competition.rounds[0];
		const round_object_id = round ? round_id : null;
		// const round_object_id = round ? round._id : null;
		const round_no = round ? round.round_no : null;
		const competition_name = competition.name;

		// console.log("Competition Name round------------------------------:", round, round_object_id, round_no, competition_name);

		let participantInstance;
		let stripePaymentDetails;
		const existingParticipant = await ParticipantModel.findOne({
			participant_object_id: user?._id,
			competition_object_id: competition_object_id,
			round_object_id: round_object_id
		});
		// console.log("existingParticipant***************************", existingParticipant);

		if (existingParticipant?.participant_payment_status === 'PENDING' || existingParticipant?.participant_payment_status === null) {
			const stripePaymentIntent = await createStripePaymentIntents(user._id.toString(), competition_object_id, round?.price, "DEBITED", "COMPETITION PARTICIPATION");
			stripePaymentDetails = stripePaymentIntent.payment_data;
			if (!stripePaymentIntent || !stripePaymentIntent.payment_status) {
				return res.status(StatusCodes.BAD_REQUEST).json({
					message: MESSAGE.post.custom("Failed to create payment intent.")
				});
			}
			const paymentData = {
				participant_payment_status: stripePaymentIntent.payment_status,
				participant_payment_intant_id: stripePaymentIntent.payment_intent_id,
				transaction_object_id: stripePaymentIntent.id
			};
			participantInstance = await ParticipantModel.findByIdAndUpdate(existingParticipant?._id, paymentData, { new: true, runValidators: true });

		} else if (existingParticipant?.participant_payment_status === 'SUCCESS') {
			return res.status(StatusCodes.CONFLICT).json({
				message: MESSAGE.custom("You are already a participant in this round."),
			});
		} else {
			const stripePaymentIntent = await createStripePaymentIntents(user._id.toString(), competition_object_id, round?.price, "DEBITED", "COMPETITION PARTICIPATION");
			stripePaymentDetails = stripePaymentIntent.payment_data;
			const data = {
				participant_object_id: user._id,
				competition_object_id: competition_object_id,
				round_object_id: round_object_id,
				competition_name: competition_name,
				round_no: round_no,
				participant_name: user?.first_name + ' ' + user?.last_name,
				participant_payment_status: stripePaymentIntent?.payment_status,
				participant_payment_intant_id: stripePaymentIntent?.payment_intent_id,
				transaction_object_id: stripePaymentIntent?.id
			};

			// console.log("Participant Data-------------------||||||||", data);

			participantInstance = await ParticipantModel.create(data);
		}

		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: { participantInstance, stripePaymentDetails }
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};

export const participantRePayment = async (req: Request, res: Response): Promise<any> => {
	const { competition_object_id, round_object_id } = req.body;
	try {
		// Retrieve user details and validate
		const user = await getDetailsByEmail(req.user.email);
		if (!user?._id) {
			return res.status(StatusCodes.NOT_FOUND).json({ message: "User not found." });
		}
		// Find the competition and its first round
		const competition = await CompetitionModel.findById(competition_object_id).select('name rounds');
		if (!competition || !competition.rounds.length) {
			return res.status(StatusCodes.NOT_FOUND).json({ message: "Competition or round not found." });
		}
		const round = competition.rounds[0];
		// Check for existing participant
		const existingParticipant = await ParticipantModel.findOne({
			participant_object_id: user._id,
			competition_object_id,
			round_object_id
		});
		// Create a Stripe payment intent
		const stripePaymentIntent = await createStripePaymentIntents(
			user._id.toString(),
			competition_object_id,
			round.price,
			"DEBITED",
			"COMPETITION PARTICIPATION"
		);
		// Prepare payment data for updating the participant
		const paymentData = {
			participant_payment_status: stripePaymentIntent.payment_status,
			participant_payment_intant_id: stripePaymentIntent.payment_intent_id,
			transaction_object_id: stripePaymentIntent.id
		};
		// Update existing participant payment details or handle non-existing participant
		if (existingParticipant) {
			const updatedPayment = await ParticipantModel.findByIdAndUpdate(
				existingParticipant._id,
				{ $set: paymentData },
				{ new: true, runValidators: true } // Removed upsert to ensure only updating
			);
			return res.status(StatusCodes.CREATED).json({
				message: MESSAGE.post.succ,
				result: { updatedPayment, stripePaymentDetails: stripePaymentIntent.payment_data }
			});
		}
		return res.status(StatusCodes.NOT_FOUND).json({
			message: MESSAGE.post.fail,
			result: {}
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: "An error occurred during the payment process.",
			error: error
		});
	}
};
export const verifyPayment = async (req: Request, res: Response): Promise<any> => {
	const { id } = req.params;
	const { paymentIntent_id } = req.body;
	// Validate paymentIntent_id
	if (!paymentIntent_id) {
		return res.status(StatusCodes.UNPROCESSABLE_ENTITY).json({
			status: false,
			message: "Stripe order Id is required",
			data: {}
		});
	}
	try {
		// Retrieve the existing participant
		const existingParticipant = await ParticipantModel.findById(id).lean();
		if (!existingParticipant) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Participant not found.",
				data: {}
			});
		}
		// Retrieve the Payment Intent from Stripe
		const paymentIntent = await verifyPaymentViaStripe(paymentIntent_id);
		// Check if the Payment Intent exists and is successful
		if (paymentIntent && paymentIntent.status === "succeeded") {
			const paymentData = {
				participant_payment_status: paymentIntent.status === "succeeded" ? TRANSACTION_STATUS.success : TRANSACTION_STATUS.cancel, // Correctly set the payment status
			};
			// Update the participant's payment status
			const updatedPayment = await ParticipantModel.findByIdAndUpdate(
				existingParticipant._id,
				{ $set: paymentData },
				{ new: true, runValidators: true }
			);
			// Prepare transaction payload
			const transactionPayload = {
				id: existingParticipant.transaction_object_id,
				payment_status: paymentIntent?.status === "succeeded" ? TRANSACTION_STATUS.success : TRANSACTION_STATUS.cancel,
				respons_data: paymentIntent
			};
			// Update transaction status
			const data = await transactionStatusUpdate(transactionPayload);
			// if (data?.payment_status === "SUCCESS") {
			return res.status(StatusCodes.OK).json({
				message: MESSAGE.custom("Transaction fetched successfully!"),
				result: data
			});
			// }
		} else {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Payment not successful!"),
				data: {}
			});
		}
	} catch (error) {
		console.error("Payment verification error:", error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.custom("Transaction unsuccessful!"),
			error: error
		});
	}
};

export const participantCompetitionDetails = async (req: Request, res: Response): Promise<any> => {
	try {
		const competitionId = req.params.competitionId;

		// Fetch participant details using email
		const participantDetails = await getDetailsByEmail(req.user.email);
		if (!participantDetails) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Participant details not found."
			});
		}

		// Get the current round for the competition
		const current_round = await currentRound(competitionId.toString(), res);
		if (!current_round) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Current round not found."
			});
		}

		// Fetch competition details for the participant
		const fetchCompetitionDetails = await ParticipantModel.find({
			competition_object_id: competitionId,
			participant_object_id: participantDetails._id
		})
			.populate('competition_object_id')
			.populate('transaction_object_id')
			.populate('participant_object_id')
			.populate('round_object_id')
			.populate('round_object_id.additional_vote_package')
			.lean();

		if (!fetchCompetitionDetails || fetchCompetitionDetails.length === 0) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "No competition details found for the participant."
			});
		}
		const additional_vote_packages = await VotePackageModel.find({
			_id: { $in: current_round.additional_vote_package.toString() }
		}).lean();


		// Enrich the competition details with current round information
		const enrichedCompetitionDetails = fetchCompetitionDetails.map(participant => ({
			...participant,
			current_round,
			additional_vote_packages
			// current_round_details: currentRoundDetails,
		}));

		// Return the enriched competition details
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: enrichedCompetitionDetails
		});
	} catch (error) {
		console.error("Error fetching participant competition details:", error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.get.fail,
			error: "An error occurred while fetching competition details."
		});
	}
};

export const upcommingCompetitionList = async (req: Request, res: Response): Promise<any> => {
	try {
		const currentDate = new Date();
		const userDetails = await getDetailsByEmail(req.user.email);
		//  Fetch upcoming competitions
		const fetchUpcomingCompetitions = await CompetitionModel.find({
			status: "ACTIVE",
			challenge_start_date: { $gte: currentDate }
		})
			.populate("creator_company")
			.populate("created_by")
			.populate("competition_type")
			.lean();
		if (!fetchUpcomingCompetitions.length) {
			return res.status(StatusCodes.OK).json({
				message: "No upcoming competitions found.",
				Result: []
			});
		}
		//  Fetch participant details
		const participant_object_id = userDetails?._id;
		const savedCompetitions = await SavedCompetitionModel.find({ participant_object_id }).lean();
		//  Get saved competitions as a Set for quick lookup
		const savedCompetitionIds = new Set(
			savedCompetitions.map((comp) => comp.competition_object_id.toString())
		);
		//  Process each competition to include:
		//    - Current round (dynamic based on competition ID)
		//    - Participant details (dynamic based on round ID)
		const competitionsWithMetadata = await Promise.all(
			fetchUpcomingCompetitions.map(async (competition) => {
				//  Each competition gets its own `current_round`
				const current_round = await currentRound(competition._id.toString(), res);
				//  Get participant details for CURRENT ROUND + USER
				const participantDetails = await ParticipantModel.find({
					round_object_id: current_round?._id,
					participant_object_id,
					competition_object_id: competition?._id.toString()
				}).lean();
				const enrolled_competition = participantDetails.some(participant =>
					participant.participant_payment_status == "SUCCESS"
				);
				console.log("participantDetails-----------------------------", current_round?._id, participant_object_id, competition?._id.toString(), participantDetails);
				return {
					...competition,
					competition_saved_status: savedCompetitionIds.has(
						competition._id.toString()
					),
					current_round, //  Dynamic round
					participantDetails,
					enrolled_competition //  Dynamic participant data
				};
			})
		);
		//  Final Response
		return res.status(StatusCodes.OK).json({
			message: "Fetched upcoming competitions with metadata",
			Result: competitionsWithMetadata,
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error: error,
		});
	}
};
export const ongoingCompetitionList = async (req: Request, res: Response): Promise<any> => {
	try {
		const userDetails = await getDetailsByEmail(req.user.email);
		const fetchOngoingCompetitions = await CompetitionModel.find({
			status: "ACTIVE",
			$and: [
				{ challenge_start_date: { $lte: new Date() } }, // current datetime is after or equal to start
				{ challenge_end_date: { $gte: new Date() } }   // current datetime is before or equal to end
			]
		}).populate('creator_company') // Populate the creator_company field
			.populate('created_by')      // Populate the created_by field
			.populate('competition_type') // Populate the competition_type field
			.lean();
		const participant_object_id = userDetails?._id;
		const savedCompetitions = await SavedCompetitionModel.find({ participant_object_id }).lean();
		// Create a set of saved competition IDs for quick lookup
		const savedCompetitionIds = new Set(savedCompetitions.map(comp => comp.competition_object_id.toString()));

		const competitionsWithMetadata = await Promise.all(
			fetchOngoingCompetitions.map(async (competition) => {
				// Each competition gets its own `current_round`
				const current_round = await currentRound(competition._id.toString(), res);
				// Get participant details for CURRENT ROUND + USER
				const participantDetails = await ParticipantModel.find({
					round_object_id: current_round?._id,
					participant_object_id,
					competition_object_id: competition?._id.toString()
				}).lean();
				// Determine if the participant has uploaded content
				const hasUploadedContent = participantDetails.some(participant =>
					participant.content.files && participant.content.files.length > 0
				);
				const enrolled_competition = participantDetails.some(participant =>
					participant.participant_payment_status == "SUCCESS"
				);
				return {
					...competition,
					competition_saved_status: savedCompetitionIds.has(competition._id.toString()),
					current_round, // Dynamic round
					participantDetails, // Dynamic participant data
					hasUploadedContent, enrolled_competition // Boolean indicating if content was uploaded
				};
			})
		);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitionsWithMetadata
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error: error // Include error message if available
		});
	}
};
export const saveCompetition = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_object_id } = req.body
		if (!competition_object_id) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition ID required."
			});
		}
		let user = await getDetailsByEmail(req.user.email);
		// Check if the record already exists
		const savedCompetitionData = {
			competition_object_id,
			participant_object_id: user?._id
		};
		const existingEntry = await SavedCompetitionModel.findOne({
			competition_object_id,
			participant_object_id: user?._id
		});
		console.log("existingEntry", existingEntry);
		if (existingEntry) {
			return res.status(StatusCodes.CONFLICT).json({
				message: "This competition is already saved by the participant."
			});
		}
		const saveCompetitionInstance = await SavedCompetitionModel.create(savedCompetitionData);
		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: saveCompetitionInstance
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};
export const saveCompetitionList = async (req: Request, res: Response): Promise<any> => {
	try {
		const user = await getDetailsByEmail(req.user.email);
		const participant_object_id = user?._id;
		const savedCompetitions = await SavedCompetitionModel.find({ participant_object_id }).populate('competition_object_id').populate('participant_object_id').lean();
		if (!savedCompetitions) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "No saved competitions found for this participant."
			});
		}
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: savedCompetitions
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error: error // Include error message if available
		});
	}
};
export const removeCompetition = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_object_id } = req.body
		if (!competition_object_id) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition ID required."
			});
		}
		let user = await getDetailsByEmail(req.user.email);
		// Check if the record already exists
		const participant_object_id = user?._id;
		const deletedCompetition = await SavedCompetitionModel.findOneAndDelete({
			competition_object_id,
			participant_object_id
		});
		if (!deletedCompetition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found.",
			});
		}
		return res.status(StatusCodes.OK).json({
			message: "Competition deleted successfully.",
			result: deletedCompetition
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
}
export const uploadContent = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_object_id, round_object_id, description } = req.body;
		if (!req.files || !("content" in req.files)) {
			return res.status(422).json({
				message: MESSAGE.post.custom("Content not found")
			});
		}
		const files = req.files['content'] as Express.Multer.File[];
		if (!files || files.length === 0) {
			return res.status(422).json({
				message: MESSAGE.post.custom("Content files not found")
			});
		}
		const filesData = await Promise.all(files.map(async (file) => {
			const contentName = file.originalname;
			const contentBuffer = file.buffer;
			const fileType = contentName.split('.').pop();
			const isValidFileType = fileType ? await validateFileType(competition_object_id, fileType) : null;
			if (!isValidFileType) {
				return res.status(422).json({
					message: MESSAGE.post.custom("Invalid file type")
				});
			}
			const contentUrl = await uploadImageToS3Service("competition", contentName, contentBuffer);
			return { url: contentUrl, name: contentName };
		}));
		const content = { description, upload_date_time: new Date(), files: filesData };
		const user = await getDetailsByEmail(req.user.email)
		if (!user) {
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: 'User not authenticated.'
			});
		}
		const updatedParticipation = await ParticipantModel.findOneAndUpdate(
			{ competition_object_id, round_object_id, participant_object_id: user._id },
			{ content },
			{ new: true }
		);
		if (!updatedParticipation) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: "Competition not found",
			});
		}
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: updatedParticipation
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};
const validateFileType = async (competitionId: string, fileType: string): Promise<boolean> => {
	const competition = await CompetitionModel.findById(competitionId).exec();
	if (!competition) {
		throw new Error("Competition not found");
	}
	return competition.file_type.map(type => type.toLowerCase()).includes(fileType.toLowerCase());
};
export const addVote = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_object_id, round_object_id, participant_object_id } = req.body;
		const user = await getDetailsByEmail(req.user.email);
		const freeVoteAvailability = await VoteModel.findOne({
			competition_object_id,
			round_object_id,
			voter_object_id: user?._id,
			voting_type: "FREE"
		})
		if (freeVoteAvailability) {
			const toObjectId = (id: string) => new mongoose.Types.ObjectId(id);
			const subscriptionAvailability = await VotingSubscriptionModel.aggregate([
				{
					$match: {
						competition_object_id: toObjectId(competition_object_id),
						round_object_id: toObjectId(round_object_id),
						participant_object_id: toObjectId(participant_object_id),
						member_object_id: user?._id,
					},
				},
				{
					$group: {
						_id: null,
						totalVotes: { $sum: "$number_of_votes" },
					},
				},
			]);
			const totalAvailableVotes = subscriptionAvailability[0]?.totalVotes || 0;
			const totalGivenVotes = await VoteModel.find({
				competition_object_id,
				round_object_id,
				voter_object_id: user?._id,
				participant_object_id: participant_object_id,
				voting_type: "PAID"
			})
			if (totalAvailableVotes <= totalGivenVotes.length) {
				return res.status(422).json({
					message: MESSAGE.post.custom("Please buy extra voting package for further process!!!")
				});
			}
		}
		const newVote = new VoteModel({
			competition_object_id,
			round_object_id,
			participant_object_id,
			voter_object_id: user?._id,
			voting_type: (!freeVoteAvailability) ? "FREE" : "PAID"
		});
		await newVote.save();
		return res.status(StatusCodes.CREATED).json({
			message: "Vote added successfully.",
			Result: newVote
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};
export const addVoteSubscription = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_object_id, round_object_id, participant_object_id, number_of_votes, votes_price, transaction_id } = req.body;
		const user = await getDetailsByEmail(req.user.email);
		const newVote = new VotingSubscriptionModel({
			competition_object_id,
			round_object_id,
			participant_object_id,
			number_of_votes,
			votes_price,
			transaction_id,
			member_object_id: user?._id,
			paymentStatus: "PAID"
		});
		await newVote.save();
		return res.status(StatusCodes.CREATED).json({
			message: "Vote package subscription successful.",
			Result: newVote
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};
// **************************Common Controller*************************************************************************
export const getParticipantRoundStatuses = async (req: Request, res: Response): Promise<any> => {
	try {
		const userDetails = await getDetailsByEmail(req.user.email);
		if (!userDetails) {
			return res.status(StatusCodes.UNAUTHORIZED).json({ message: 'User not authenticated.' });
		}
		const statusFilter = (req.query.status || req.params.status) as string;
		const now = dayjs();
		const participants = await ParticipantModel.find({
			participant_object_id: userDetails._id,
			status: { $in: ['PARTICIPATING'] }
		})
			.populate('competition_object_id')
			.populate('participant_object_id') // Limit participant fields
			.populate('transaction_object_id')
			.lean();
		const result = await Promise.all(participants.map(async (p: any, index: number) => {
			const comp = p.competition_object_id;
			if (!comp || !Array.isArray(comp.rounds)) return null;
			const round = comp.rounds.find((r: any) => r.round_no === p.round_no);
			if (!round) return null;
			const roundStatus = get_round_status(round, now);
			const time_status = getTimeStatus(round, now);
			// Determine simplified round status
			let status: 'upcoming' | 'ongoing' | 'completed' = 'completed';
			if (dayjs(round.start_date_time).isAfter(now)) {
				status = 'upcoming';
			} else if (
				dayjs(round.start_date_time).isBefore(now) &&
				dayjs(round.end_date_time).isAfter(now)
			) {
				status = 'ongoing';
			}
			return {
				current_rounds: [
					{
						...round,
						roundStatus,
						time_status: time_status,
						status: status
					}
				],
				competition: comp,
				round_no: round.round_no,
				participant: {
					...p.participant_object_id,
					participantRoundStatus: participants.length > 0 ? participants[0].status : null,
					participantRoundPaymentStatus: participants.length > 0 ? participants[0].participant_payment_status : null
				},
			};
		}));
		// Filter out nulls (where no round or comp was matched)
		const filtered = result.filter(Boolean);
		// Apply status filter if provided
		let filteredResult = filtered;
		if (['upcoming', 'ongoing', 'completed'].includes(statusFilter)) {
			filteredResult = filtered.filter(r => r?.current_rounds[0]?.status === statusFilter);
			console.log("Filtered Result:", filteredResult);
		}
		if (filteredResult.length === 0) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: 'No participant rounds found for the specified status.'
			});
		}
		return res.status(StatusCodes.OK).json({
			message: 'Participant round status fetched successfully',
			result: filteredResult
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: "An error occurred during the payment process.",
			error: error
		});
	}
};
export const getAllparticipantsList = async (req: Request, res: Response): Promise<any> => {
	try {
		const competitionId = req.params.competitionId;
		const roundId = req.params.roundId;
		if (!competitionId) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: 'Competition ID is required.'
			});
		}
		if (!roundId) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: 'Round ID is required.'
			});
		}
		// Fetch competition details with matching rounds
		const competition = await CompetitionModel.findById(competitionId)
			.populate({
				path: 'rounds',
				match: { _id: roundId }  // Filter rounds by roundId
			})
			.lean();
		if (!competition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: 'Competition not found.'
			});
		}
		// Check if the rounds array contains the matched round
		const matchedRounds = competition.rounds.filter(round => round._id.toString() === roundId);
		if (matchedRounds.length === 0) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: 'No matching rounds found for this competition.'
			});
		}
		const roundsWithStatus = await Promise.all(matchedRounds.map(async (round) => ({
			...round,
			roundStatus: await get_round_status(dayjs(round.start_date_time), dayjs(round.end_date_time))
		})));
		// Fetch participants for the specific competition and round
		const participants = await ParticipantModel.find({
			competition_object_id: competitionId,
			round_object_id: roundId,
			status: { $in: [PARTICIPATION_STATUS.participating] }, //PARTICIPATION_STATUS.participated,
			participant_payment_status: { $eq: "SUCCESS" }
		})
			.populate('participant_object_id') // Populate participant details
			.lean();
		// Format the participant details
		const formattedParticipants = participants;
		// Return the competition details, matched rounds with status, and participants
		return res.status(StatusCodes.OK).json({
			message: 'Competition details with participants fetched successfully',
			competition: {
				_id: competition._id,
				name: competition.name,
				description: competition.description,
				rounds: roundsWithStatus,
				participants: formattedParticipants
			}
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: "An error occurred during the payment process.",
			error: error
		});
	}
};
export const getCompetitionDetailsWithRoundAndParticipants = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competitionId, roundId } = req.params;
		// Get user details
		const userDetails = await getDetailsByEmail(req.user.email);
		if (!userDetails) {
			return res.status(StatusCodes.UNAUTHORIZED).json({ message: "User not authenticated" });
		}
		// Validate input parameters
		if (!competitionId || !roundId) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: !competitionId ? 'Competition ID is required.' : 'Round ID is required.'
			});
		}
		const now = dayjs();
		// Fetch competition with filtered round
		const competition = await CompetitionModel.findById(competitionId)
			.populate({ path: 'rounds', match: { _id: roundId } })
			.lean();
		if (!competition || !competition.rounds?.length) {
			return res.status(StatusCodes.NOT_FOUND).json({ message: 'Competition or matching round not found.' });
		}
		const round = competition.rounds.find(r => r._id.toString() === roundId); // since we've matched by ID
		// Determine round status
		const roundStatus = get_round_status(round, now);
		const time_status = getTimeStatus(round, now);
		// Prepare rounds with status
		const roundsWithStatus = [{
			...round,
			roundStatus,
			time_status
		}];
		// Fetch participants
		const participants = await ParticipantModel.find({
			competition_object_id: competitionId,
			participant_object_id: userDetails._id.toString(),
			round_object_id: roundId,
			status: { $in: [PARTICIPATION_STATUS.participating] }, //PARTICIPATION_STATUS.participated,
			participant_payment_status: "SUCCESS"
		})
			.populate('participant_object_id')
			.populate('transaction_object_id')
			.lean();
		// Final result
		const result = {
			competition,
			current_rounds: roundsWithStatus,
			participants
		};
		return res.status(StatusCodes.OK).json({
			message: 'Competition details with participants fetched successfully',
			Result: result
		});
	} catch (error) {
		console.error(error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: "An error occurred during the payment process.",
			error: error
		});
	}
};
