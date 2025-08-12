import { Request, Response, Router } from "express";
import { Model } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../constants/message";
import { ROLES } from "../../../../../constants/roles/roles";
import service from "../../../../../services";
import GroupOwnerModel from "../../../../../model/groupOwner.model";
import MemberModel from "../../../../../model/member.model";
import { convertCaseInsensitiveForQuery } from "../../../../../services/auth/auth.service";
import { addMemberActivityLog } from "../../../../../services/logs/employeeLog.service";
import {
	employeeActivity,
	employeeActivityLogDescription,
	employeeActivityLogStatus
} from "../../../../../constants/activityLog/activityLog";
import { log } from "console";

export const login = async (req: Request, res: Response): Promise<any> => {
	try {
		const { user_id, password } = req.body;
		// checking if user_id is email or user_name
		const query = user_id.includes("@")
			? { email: convertCaseInsensitiveForQuery(user_id) }
			: { user_name: user_id };
		let model: Model<any>,
			jwtPayload = {};
		let userInstance = null
		let role: string | null = null

		if (req.body.role === ROLES.super_admin || req.body.role === ROLES.admin) {
			model = GroupOwnerModel;

			userInstance = await model.findOne({
				$and: [query, { role: req.body.role }]
			});

			role = userInstance?.role || req.body.role;

		} else {
			model = MemberModel;
			userInstance = await model.findOne(query);
			role = userInstance?.role || req.body.role;
		}

		if (!userInstance) {
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: MESSAGE.custom("Authentication Failed!")
			});
		}

		// Comparing given Password with Actual Password
		const passwordCompare: boolean = await service.auth.comparePassword(password, userInstance.password);
		console.log("Password Bcrypt Comparison", passwordCompare);

		if (!passwordCompare) {
			//member Password Incorrect Log
			const memberLoginActivityPayload = {
				role: userInstance.role,
				email: userInstance.email,
				first_name: userInstance.first_name,
				last_name: userInstance.last_name,
				user_name: userInstance.user_name,
				activity: employeeActivity.login,
				status: employeeActivityLogStatus.fail,
				description: employeeActivityLogDescription.password_incorrect
				//activity_initiated_by: user_id
			};
			await addMemberActivityLog(memberLoginActivityPayload);
			return res.status(StatusCodes.UNAUTHORIZED).json({
				message: MESSAGE.custom("Authentication Failed!")
			});
		}

		// Creating JWT Payload for JWT generation
		if (role === ROLES.super_admin || role === ROLES.admin) {

			// Updating Last login date with current system date
			userInstance.last_login_date = Date.now();
			// userInstance.devices_token = devices_token ?? null;
			// Creating JWT payload for Group Owner login
			jwtPayload = {
				//_id: userInstance._id,
				email: userInstance.email,
				role: userInstance.role,
				first_name: userInstance.first_name,
				last_name: userInstance.last_name,
				user_name: userInstance.user_name
			};
		} else if (role === ROLES.competition_creator || role === ROLES.participant || role === ROLES.individual_voter || role === ROLES.member) {
			// Updating Last login date with current system date
			userInstance.last_login_date = Date.now();
			userInstance.employee_status = "ACTIVE";
			// userInstance.devices_token = devices_token ?? null;

			// Creating JWT payload for member login
			jwtPayload = {
				//_id: userInstance._id,
				email: userInstance.email,
				role: userInstance.role,
				first_name: userInstance.first_name,
				last_name: userInstance.last_name,
				user_name: userInstance.user_name
			};
		}
		// Generating JWT
		const token: string = await service.auth.generateJWT(jwtPayload);
		// Updating the database
		const loginInstance = await model.findByIdAndUpdate(userInstance._id, userInstance);

		if (!loginInstance) {
			if (role === ROLES.competition_creator || role === ROLES.participant || role === ROLES.individual_voter || role === ROLES.member) {
				//member Unsuccessfull Login Log
				const memberLoginActivityPayload = {
					role: userInstance.role,
					email: userInstance.email,
					first_name: userInstance.first_name,
					last_name: userInstance.last_name,
					user_name: userInstance.user_name,
					activity: employeeActivity.login,
					status: employeeActivityLogStatus.fail,
					description: employeeActivityLogDescription.login_unsuccessful,
					activity_initiated_by: user_id
				};
				await addMemberActivityLog(memberLoginActivityPayload);
			}

			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.none
			});
		}

		//member Successfull Login Log
		if (role === ROLES.competition_creator || role === ROLES.participant || role === ROLES.individual_voter || role === ROLES.member) {
			const memberLoginActivityPayload = {
				role: userInstance.role,
				email: userInstance.email,
				first_name: userInstance.first_name,
				last_name: userInstance.last_name,
				user_name: userInstance.user_name,
				activity: employeeActivity.login,
				status: employeeActivityLogStatus.pass,
				description: employeeActivityLogDescription.login_successful,
				activity_initiated_by: user_id
			};
			await addMemberActivityLog(memberLoginActivityPayload);
		}
		// const today = new Date();
		// const day = today.getDate();
		// const month = today.getMonth() + 1;
		// const year = today.getFullYear();

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.post.succAuth,
			result: {
				loginInstance,
				token
			},
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Login Unsuccessful!"),
			error
		});
	}
};



