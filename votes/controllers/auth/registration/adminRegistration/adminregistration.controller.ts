import { Request, Response } from "express";
import { Model } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../../constants/message";
import { ROLES } from "../../../../../../constants/roles/roles";
import service from "../../../../../../services";
import GroupOwnerModel from "../../../../../../model/groupOwner.model";

export const adminRegistration = async (req: Request, res: Response): Promise<any> => {
	try {
		const {
			role,
			first_name,
			middle_name,
			last_name,
			user_name,
			password,
			email,
			gender,
			address_line_1,
			address_line_2,
			city,
			state,
			country,
			ZIP,
			contact_label,
			phone_number,
			phone_extension
		} = req.body;
		const emailExists = await GroupOwnerModel.findOne({ email: email });

		if (!emailExists) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Email Not Registered! Contact to the Group Owner")
			});
		} else if (emailExists.is_registered == true) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Duplicate Email! User Already Exist")
			});
		}
		if (await service.auth.isDuplicateValueCheckService(GroupOwnerModel, 'phone_number', phone_number)) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom(`Duplicate Phone Number! ${phone_number} already exists!`)
			});
		}
		if (await service.auth.isDuplicateValueCheckService(GroupOwnerModel, 'user_name', user_name)) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom(`Duplicate UserName! ${user_name} already exists!`)
			});
		}
		const user = await GroupOwnerModel.findOneAndUpdate(
			{ email: email }, // Condition to find the member by email
			{
				role: "ADMIN",
				first_name,
				middle_name,
				last_name,
				user_name,
				password: await service.auth.hashPassword(password),
				email,
				gender,
				address_line_1,
				address_line_2,
				city,
				state,
				country,
				ZIP,
				contact_label,
				phone_number,
				phone_extension,
				is_registered: true
			},
			{ new: true, upsert: true, runValidators: true } // Options
		);

		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.custom("Registration Successful!"),
			user: user
		});

	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Registration Unsuccessful!"),
			error
		});
	}
};

export const addAdmin = async (req: Request, res: Response): Promise<any> => {
	try {
		const {
			first_name,
			last_name,
			phone_number,
			gender,
			email,
		} = req.body;

		if (await service.auth.isDuplicateGroupOwnerEmailService(GroupOwnerModel, email)) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom(`Duplicate Email! ${email} already exists!`)
			});
		}
		const role = "ADMIN";
		const user = new GroupOwnerModel({
			role,
			first_name,
			last_name,
			email,
			phone_number,
			gender,
		});

		await user.save();

		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.custom("Registration Successful!"),
			user: user // Return relevant user info
		});

	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Registration Unsuccessful!"),
			error
		});
	}
};

export const listAdmin = async (req: Request, res: Response): Promise<any> => {
	try {
		const admin = await GroupOwnerModel.find({ role: "ADMIN" });
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("List of Admin"),
			admin
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("List of Admin Unsuccessful!"),
			error
		});
	}
};