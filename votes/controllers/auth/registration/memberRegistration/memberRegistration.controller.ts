import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../../constants/message";
import service from "../../../../../../services";
import MemberModel from "../../../../../../model/member.model";
import { MEMBER_STATUS } from "../../../../../../constants/status/status";
import { ROLES } from "../../../../../../constants/roles/roles";
import { uploadImageBase64Service } from "../../../../../../services/uploadBase64Image/base64Image.service";

export const memberRegistration = async (req: Request, res: Response): Promise<any> => {
	try {
		// Destructure request body
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
			zip,
			contact_label,
			phone_number,
			phone_extension,
			upload_front_side,
			upload_back_side
		} = req.body;

		const emailExists = await MemberModel.findOne({ email: email, is_verified: true });

		if (!emailExists) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Email is not verified!")
			});
		}

		// Validate required fields
		const requiredFields = [first_name, last_name, user_name, password, email, phone_number];
		if (requiredFields.some((field) => !field)) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("first_name, last_name, user_name, password, email, phone_number are required")
			});
		}

		// Check for duplicates
		const duplicateFields = ["phone_number", "user_name"];
		for (const field of duplicateFields) {
			if (await service.auth.isDuplicateValueCheckService(MemberModel, field, req.body[field])) {
				// if (await isDuplicateValue(field, req.body[field])) {
				return res.status(StatusCodes.CONFLICT).json({
					message: MESSAGE.custom(`Duplicate ${field.replace("_", " ")}! ${req.body[field]} already exists!`)
				});
			}
		}

		let uploadFrontSide = "";
		let uploadBackSide = "";
		if (role === "COMPETITION CREATOR") {
			uploadFrontSide = await uploadImageBase64Service(upload_front_side, "photoId");
			uploadBackSide = await uploadImageBase64Service(upload_back_side, "photoId");

		}
		//check role wise make prefix use switch case
		let prefix = "";
		switch (role) {
			case "COMPETITION CREATOR":
				prefix = "C";
				break;
			case "INDIVIDUAL VOTER":
				prefix = "V";
				break;
			case "PARTICIPANT":
				prefix = "P";
				break;
			default:
				return res.status(StatusCodes.BAD_REQUEST).json({
					message: MESSAGE.custom("Invalid role!")
				});
		}

		// Generate member_id
		const generateMemberId = await service.common.generateId(MemberModel, "member_id", prefix, 6);
		// Hash the password
		const hashPassword = await service.auth.hashPassword(password);
		const isApprovedbyAdmin = role === ROLES.competition_creator ? MEMBER_STATUS.pending : MEMBER_STATUS.approved;
		// Create member data
		const memberData = {
			member_id: generateMemberId,
			role,
			first_name,
			middle_name,
			last_name,
			user_name,
			password: hashPassword,
			email,
			gender,
			address_line_1,
			address_line_2,
			city,
			state,
			country,
			zip,
			contact_label,
			phone_number,
			phone_extension,
			is_registered: true, // Set is_registered to true
			is_approved: isApprovedbyAdmin,
			upload_front_side: uploadFrontSide,
			upload_back_side: uploadBackSide
		};

		// new: true: Returns the updated document instead of the original document.
		// upsert: true: Creates a new document if no matching document is found.
		// runValidators: true: Applies schema validation rules during the update operation.

		// Create or update the member
		const memberInstance = await MemberModel.findOneAndUpdate(
			{ email: email, is_verified: true }, // Query to find the member
			memberData, // Data to update or create
			{ new: true, upsert: true, runValidators: true } // Options
		);

		return res.status(StatusCodes.CREATED).json({
			message: MESSAGE.post.succ,
			result: memberInstance // Return relevant user info
		});
	} catch (error) {
		console.error("Registration error:", error); // Log the error for debugging
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.custom("Email verification failed!"),
			error
		});
	}
};

export const makeMemberRoleUpgradeRequest = async (req: Request, res: Response): Promise<any> => {
	try {
		const { member_id, role, upload_front_side, upload_back_side, address } = req.body;
		let jwtPayload: any = {};

		// Validate required fields
		if (!member_id || !role) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Member ID and role are required.")
			});
		}

		// Check if the member exists
		const member = await MemberModel.findOne({ _id: member_id });
		if (!member) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.custom("Member not found.")
			});
		}

		// Check if the current role is invalid for upgrade
		if (member.role !== ROLES.competition_creator || member.role !== ROLES.participant) {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Invalid role.")
			});
		}

		// Update the member's last login date
		member.last_login_date = new Date();

		// Prepare JWT payload
		jwtPayload = {
			email: member.email,
			role: role, // Use the new role being assigned
			first_name: member.first_name,
			last_name: member.last_name,
			user_name: member.user_name
		};

		// Set approval status based on the new role
		if (role === ROLES.competition_creator) {
			member.upload_front_side = await uploadImageBase64Service(upload_front_side, "photoId");
			member.upload_back_side = await uploadImageBase64Service(upload_back_side, "photoId");
			member.is_approved = MEMBER_STATUS.pending; // Set approval status to pending for competition creators
		} else {
			member.is_approved = MEMBER_STATUS.approved; // Set a default approval status for other roles
		}

		// Update previous role and current role
		member.previous_role = member.role;
		member.role = role;

		// Save the updated member
		await member.save();

		// Generate JWT
		const token: string = await service.auth.generateJWT(jwtPayload);

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Role upgrade request submitted successfully."),
			result: member,
			token
		});

	} catch (error) {
		console.error("Role upgrade request error:", error);
		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
			message: MESSAGE.custom("Failed to submit role upgrade request."),
			error: error instanceof Error ? error.message : "Unknown error"
		});
	}
}