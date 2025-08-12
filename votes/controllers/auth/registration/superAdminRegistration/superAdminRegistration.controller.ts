import { Request, Response } from "express";
import { Model } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../../constants/message";
import { ROLES } from "../../../../../../constants/roles/roles";
import service from "../../../../../../services";

import GroupOwnerModel from "../../../../../../model/groupOwner.model";
import { IGroupOwner } from "../../../../../../@types/interfaces/groupOwnerSchema.interface";
import { IObjectId } from "../../../../../../@types/objectId.interface";

export const superAdminRegistration = async (req: Request, res: Response): Promise<any> => {
	try {
		const {

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


		if (await service.auth.isDuplicateGroupOwnerEmailService(GroupOwnerModel, email)) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom(`Duplicate Email! ${email} already exists!`)
			});
		}

		if (await service.auth.isDuplicateValueCheckService(GroupOwnerModel, 'user_name', user_name)) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom(`Duplicate UserName! ${user_name} already exists!`)
			});
		}

		// Hashing Password
		const encryptedPassword: string = await service.auth.hashPassword(password);
		const superAdminRegistrationPayload = {
			role: ROLES.super_admin,
			first_name,
			middle_name,
			last_name,
			user_name,
			password: encryptedPassword,
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
			feature_permission: {
				"super_admin_registration": {
					"email": true,
					"notification": true
				},
				"admin_creation": {
					"email": true,
					"notification": true
				},
				"admin_registration": {
					"email": true,
					"notification": true
				},
				"member_creation": {
					"email": true,
					"notification": true
				},
				"member_profile_updation": {
					"email": true,
					"notification": true
				}
			}
		}

		// Create new Super Admin instance
		const superAdminRegistrationInstance: IGroupOwner & IObjectId = await new GroupOwnerModel(superAdminRegistrationPayload).save();


		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succAuth,
			result: superAdminRegistrationInstance // Return of the super admin registration result
		});

	} catch (error: any) {
		console.log("Error: ", error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.post.fail,
			error
		});
	}
};