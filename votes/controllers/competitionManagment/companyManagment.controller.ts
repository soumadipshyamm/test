import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../constants/message";
import CompanyManagementModel from "../../../../model/companyManagement.model";
import { getDetailsByEmail } from "../../../../utils/helper";


export const addCompany = async (req: Request, res: Response): Promise<any> => {
	try {
		const { name,
			description,
		} = req.body;
		const fetchMemberId = await getDetailsByEmail(req.user.email);
		if (!fetchMemberId) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.get.fail,
				error: "Competition Creator not found"
			});
		}
		const competitionInstance = await CompanyManagementModel.create({
			name,
			description,
			member_object_id: fetchMemberId?._id
		});

		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			Result: competitionInstance
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
}
export const listCompany = async (req: Request, res: Response): Promise<any> => {
	try {
		const competitions = await CompanyManagementModel.find().sort({ createdAt: -1 }).lean();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			Result: competitions
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.get.fail,
			error
		});
	}
}

export const getCompanyDetails = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const competition = await CompanyManagementModel.findById(id).lean();
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
export const updateCompany = async (req: Request, res: Response): Promise<any> => {
	try {
		const id = req.params.id;
		const {
			name,
			description
		} = req.body;
		const competition = await CompanyManagementModel.findByIdAndUpdate(id, {
			name,
			description
		});
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.put.succ,
			Result: competition
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.put.fail,
			error
		});
	}
}
export const deleteCompany = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id } = req.params;
		const competition = await CompanyManagementModel.findByIdAndDelete(id);
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
