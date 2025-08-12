import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../constants/message";
import VotePackageModel from "../../../../../model/votePackage.model";
import CompetitionModel from "../../../../../model/competition.model";


export const addVotePackage = async (req: Request, res: Response): Promise<any> => {
	try {
		const { package_name, package_type, votes_package } = req.body;

		// let votesPackageObjects = []; // Changed from const to let
		// if (votes_package.length > 0) {
		// 	votesPackageObjects = await Promise.all(
		// 		votes_package.map(async (votePackage: any) => {
		// 			return {
		// 				number_of_votes: votePackage.number_of_votes,
		// 				votes_price: votePackage.votes_price
		// 			};
		// 		})
		// 	);
		// }
		const newVotePackage = {
			package_name,
			package_type,
			// votes_package: votes_package
		};
		// Assuming VotePackageModel is imported and defined correctly
		const votePackage = await VotePackageModel.create(newVotePackage);
		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: votePackage
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
}
export const listVotePackage = async (req: Request, res: Response): Promise<any> => {
	try {
		const votePackages = await VotePackageModel.find().lean();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: votePackages
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}
export const getVotePackageDetails = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const votePackage = await VotePackageModel.findById(id).lean();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: votePackage
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}
export const updateVotePackage = async (req: Request, res: Response): Promise<any> => {
	try {
		const id = req.params.id;
		const { package_name, package_type, votes_package } = req.body;
		const updatedVotePackage = {
			package_name,
			package_type,
			votes_package
		};
		const votePackage = await VotePackageModel.findByIdAndUpdate(id, updatedVotePackage);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: votePackage
		});
	}
	catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.put.fail,
			error
		});
	}
}
export const deleteVotePackage = async (req: Request, res: Response): Promise<any> => {
	try {
		const id = req.params.id;
		const votePackage = await VotePackageModel.findByIdAndDelete(id);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.delete.succ,
			Result: votePackage
		});
	}
	catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.delete.fail,
			error
		});
	}
}


//Vote Package Price Managment
export const addVotePackagePrice = async (req: Request, res: Response): Promise<any> => {
	try {
		const { votes_package_objectId, number_of_votes, votes_price } = req.body;
		const votePackage = await VotePackageModel.findById(votes_package_objectId);

		if (!votePackage) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Vote package not found"
			});
		}
		const newVotePackagePrice = {
			number_of_votes,
			votes_price,
		};
		votePackage.votes_package.push(newVotePackagePrice);
		await votePackage.save();
		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: votePackage
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
}

export const listVotePackagePrice = async (req: Request, res: Response): Promise<any> => {
	try {
		const vote_package_id = req.params.vote_package_id;
		const votePackage = await VotePackageModel.findById(vote_package_id).lean();

		if (!votePackage) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Vote package not found"
			});
		}
		const votePackagePrices = votePackage.votes_package.map((price: { number_of_votes?: number | null; votes_price?: number | null; _id?: string }) => ({
			number_of_votes: price.number_of_votes,
			votes_price: price.votes_price,
			_id: price._id
		}));

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: votePackagePrices
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}

export const detailsVotePackagePrice = async (req: Request, res: Response): Promise<any> => {
	// try {
	const vote_package_id = req.params.package_id;
	const vote_package_price_id = req.params.package_price_id;
	const votePackage = await VotePackageModel.findById(vote_package_id).lean();
	// console.log(votePackage);
	if (!votePackage) {
		return res.status(StatusCodes.NOT_FOUND).json({
			message: MESSAGE.get.fail,
			error: "Vote package not found"
		});
	}
	//specific vote package price details show	
	const votePackagePrice = votePackage.votes_package.find((price: any) => price._id?.toString() === vote_package_price_id);
	// console.log(votePackagePrice);
	if (!votePackagePrice) {
		return res.status(StatusCodes.NOT_FOUND).json({
			message: MESSAGE.get.fail,
			error: "Vote package price not found"
		});
	}
	return res.status(StatusCodes.OK).json({
		message: MESSAGE.get.succ,
		Result: votePackagePrice
	});

}

export const updteVotePackagePrice = async (req: Request, res: Response): Promise<any> => {
	try {
		const vote_package_id = req.params.package_id;
		const vote_package_price_id = req.params.package_price_id;
		const { number_of_votes, votes_price } = req.body;
		const votePackage = await VotePackageModel.findById(vote_package_id);
		if (!votePackage) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Vote package not found"
			});
		}
		const votePackagePrice = votePackage.votes_package.find((price: any) => price._id?.toString() === vote_package_price_id);
		if (!votePackagePrice) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Vote package price not found"
			});
		}
		votePackagePrice.number_of_votes = number_of_votes;
		votePackagePrice.votes_price = votes_price;
		await votePackage.save();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: votePackage
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.put.fail,
			error
		});
	}

}

export const deleteVotePackagePrice = async (req: Request, res: Response): Promise<any> => {
	try {
		const vote_package_id = req.params.package_id;
		const vote_package_price_id = req.params.package_price_id;
		const votePackage = await VotePackageModel.findById(vote_package_id);
		if (!votePackage) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Vote package not found"
			});
		}
		const votePackagePriceIndex = votePackage.votes_package.findIndex((price: any) => price._id?.toString() === vote_package_price_id);
		if (votePackagePriceIndex === -1) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Vote package price not found"
			});
		}
		votePackage.votes_package.splice(votePackagePriceIndex, 1);
		await votePackage.save();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.delete.succ,
			Result: votePackage
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.delete.fail,
			error
		});
	}
}



// *********************************************************************************************

// export const getExtraVotePackagesList = async (req: Request, res: Response): Promise<any> => {
// 	try {
// 		const { competitionId, roundId } = req.params;

// 		// Fetch the competition by ID
// 		const competition = await CompetitionModel.findById(competitionId).lean();

// 		// Check if competition exists
// 		if (!competition) {
// 			return res.status(StatusCodes.NOT_FOUND).json({
// 				message: MESSAGE.get.fail,
// 				error: "No competition found with the provided ID"
// 			});
// 		}


// 		const round = competition.rounds.find((round: any) => round._id.toString() === roundId);

// 		// 	// Check if the round exists
// 		if (!round) {
// 			return res.status(StatusCodes.NOT_FOUND).json({
// 				message: MESSAGE.get.fail,
// 				error: "No round found with the provided ID"
// 			});
// 		}

// 		const votePackagesForRound = round?.additional_vote_package || [];

// 		console.log("Vote Packages for Round:", votePackagesForRound);


// 		const populatedVotePackages = await VotePackageModel.find({
// 			_id: votePackagesForRound
// 		}).lean();

// 		console.log("Populated Vote Packages:", populatedVotePackages);

// 		// Check if there are any vote packages for the competition

// 		return res.status(StatusCodes.OK).json({
// 			message: MESSAGE.get.succ,
// 			Result: populatedVotePackages
// 		});

// 	} catch (error) {
// 		console.error(error);
// 		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
// 			message: MESSAGE.get.fail,
// 			error: "An error occurred while fetching vote packages"
// 		});
// 	}
// };


export const getExtraVotePackagesList = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competitionId, roundId } = req.params;

		// Fetch the competition by ID
		const competition = await CompetitionModel.findById(competitionId).lean();

		// Check if competition exists
		if (!competition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Competition not found."
			});
		}

		const round = competition.rounds.find((round: any) => round._id.toString() === roundId);

		// Check if the round exists
		if (!round) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.get.fail,
				error: "Round not found."
			});
		}

		const votePackagesForRound = round.additional_vote_package || [];

		// Check if there are any vote packages for the round
		if (Array.isArray(votePackagesForRound) && votePackagesForRound.length === 0) {
			return res.status(StatusCodes.OK).json({
				message: MESSAGE.get.succ,
				Result: [],
				info: "No additional vote packages available for this round."
			});
		}

		const populatedVotePackages = await VotePackageModel.find({
			_id: { $in: votePackagesForRound } // Use $in for better performance
		}).lean();

		// Return the populated vote packages
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: populatedVotePackages
		});

	} catch (error) {
		console.error("Error fetching vote packages:", error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.get.fail,
			error: "An error occurred while fetching vote packages."
		});
	}
};