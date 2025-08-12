import { Request, Response } from "express";
import { Model } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../constants/message";
import GroupOwnerModel from "../../../../../model/groupOwner.model";
import MemberModel from "../../../../../model/member.model";

export const adminList = async (req: Request, res: Response): Promise<any> => {
	try {
		const user = await GroupOwnerModel.find({ 'role': 'ADMIN' });
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Admin fetch successfully!"),
			user
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Admin fetch unsuccessful!"),
			error
		});
	}
}
export const ownerList = async (req: Request, res: Response): Promise<any> => {
	try {
		const user = await GroupOwnerModel.find({ 'role': 'SUPER ADMIN' });
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Owner fetch successfully!"),
			user
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Owner fetch unsuccessful!"),
			error
		});
	}
}

export const memberList = async (req: Request, res: Response): Promise<any> => {
	try {
		const user = await MemberModel.find();
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Member fetch successfully!"),
			user
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Member fetch unsuccessful!"),
			error
		});
	}
}


export const updateFeaturePermission = async (req: Request, res: Response): Promise<any> => {
	
    const { feature_permission, _id } = req.body;

    try {
        const updatedOwner = await GroupOwnerModel.findByIdAndUpdate(
            _id,
            { feature_permission },
            { new: true, runValidators: true }
        );

        if (!updatedOwner) {
            return res.status(404).json({ message: 'Group owner not found' });
        }

        return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Permission updated successfully!")
		});
    } catch (error) {
        return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Something went wrong!"),
			error
		});
    }
}





