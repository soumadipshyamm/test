import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../constants/message";
import CompetitionTypeModel from "../../../../model/competitionType.model";
import { getDetailsByEmail } from "../../../../utils/helper";


export const addCompetitionType = async (req: Request, res: Response): Promise<any> => {
	try {
		const { competition_name, competition_description, total_rounds } = req.body;
		const user = await getDetailsByEmail(req.user.email);

		if (!user) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.post.fail,
				error: "User not found"
			});
		}

		// Input validation
		if (!competition_name || !competition_description || total_rounds === undefined) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.post.fail,
				error: "Missing required fields: competition_name, competition_description, total_rounds"
			});
		}

		// Validate total_rounds is a positive number
		const roundsCount = parseInt(total_rounds);
		if (isNaN(roundsCount) || roundsCount <= 0) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.post.fail,
				error: "total_rounds must be a positive number"
			});
		}

		// Generate rounds_details based on total_rounds count
		const rounds_details = Array.from({ length: roundsCount }, (_, index) => ({
			round_no: index + 1,
			price: 0
		}));

		// Create competition with all data including created_by
		const competitionInstance = await CompetitionTypeModel.create({
			competition_name,
			competition_description,
			total_rounds: roundsCount,
			rounds_details,
			created_by: user._id
		});

		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: competitionInstance
		});

	} catch (error) {
		console.error('Error creating competition type:', error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.post.fail,
			error: error instanceof Error ? error.message : 'Unknown error occurred'
		});
	}
};
export const listCompetitionTypes = async (req: Request, res: Response): Promise<any> => {
	try {

		const page = parseInt(req.query.page as string) || 1;
		const limit = parseInt(req.query.limit as string) || 10;
		const skip = (page - 1) * limit;

		const competitions = await CompetitionTypeModel.find()
			.sort({ createdAt: -1 })
			.skip(skip)
			.limit(limit)
			.lean();

		const totalCount = await CompetitionTypeModel.countDocuments();

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitions,
			pagination: {
				totalCount,
				page,
				limit,
				totalPages: Math.ceil(totalCount / limit),
			}
		});
		// const competitions = await CompetitionTypeModel.find().sort({ createdAt: -1 }).lean();
		// return res.status(StatusCodes.OK).json({
		// 	message: MESSAGE.get.succ,
		// 	Result: competitions
		// });
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}
export const getCompetitionTypeDetails = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const competition = await CompetitionTypeModel.findById(id).lean();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competition
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}
export const updateCompetitionType = async (req: Request, res: Response): Promise<any> => {
	try {
		const id = req.params.id;

		// Validate request body
		const { competition_name, competition_description, total_rounds } = req.body;

		if (!competition_name || !competition_description || total_rounds === undefined) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: 'All fields are required: competition_name, competition_description, total_rounds.'
			});
		}

		// Update competition type
		const competition = await CompetitionTypeModel.findByIdAndUpdate(
			id,
			{
				competition_name,
				competition_description,
				total_rounds
			},
			{ new: true } // Return the updated document
		);

		// Check if competition type was found and updated
		if (!competition) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: 'Competition type not found.'
			});
		}

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			result: competition
		});
	} catch (error) {
		console.error('Error updating competition type:', error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.put.fail,
			error: error
		});
	}
}
export const deleteCompetitionType = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const competition = await CompetitionTypeModel.findByIdAndDelete(id);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.delete.succ,
			Result: competition
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.delete.fail,
			error
		});
	}
}
// export const getCompetitionTypeSearch = async (req: Request, res: Response): Promise<any> => {
// 	try {
// 		const { search } = req.query;
// 		const competition = await CompetitionTypeModel.find({ competition_name: { $regex: search, $options: 'i' } }).lean();
// 		return res.status(StatusCodes.OK).json({
// 			message: MESSAGE.get.succ,
// 			Result: competition
// 		});
// 	} catch (error) {
// 		console.log(error);
// 		return res.status(StatusCodes.BAD_REQUEST).json({
// 			message: MESSAGE.get.fail,
// 			error
// 		});
// 	}
// }
export const getCompetitionTypeSearch = async (req: Request, res: Response): Promise<any> => {
	try {
		// Get pagination parameters
		const page = parseInt(req.query.page as string) || 1;
		const limit = parseInt(req.query.limit as string) || 10;
		const skip = (page - 1) * limit;

		// Get search criteria from query parameters
		const { name, status } = req.query;

		// Build the filter object
		const filter: any = {};
		if (name) {
			filter.name = { $regex: new RegExp(name as string, 'i') }; // Case-insensitive search
		}
		if (status) {
			filter.status = status;
		}

		// Fetch competitions with pagination and filtering
		const competitions = await CompetitionTypeModel.find(filter)
			.sort({ createdAt: -1 })
			.skip(skip)
			.limit(limit)
			.lean();

		// Get total count of competitions for pagination info
		const totalCount = await CompetitionTypeModel.countDocuments(filter);

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitions,
			pagination: {
				totalCount,
				page,
				limit,
				totalPages: Math.ceil(totalCount / limit),
			}
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}


//Round Management
