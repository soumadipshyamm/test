import { Request, Response } from "express";
import { Model } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../constants/message";
import { ROLES } from "../../../../../constants/roles/roles";
import service from "../../../../../services";
import {
	employeeActivity,
	employeeActivityLogDescription,
	employeeActivityLogStatus
} from "../../../../../constants/activityLog/activityLog";
import MemberModel from "../../../../../model/member.model";
import { isDuplicateGroupOwnerEmailService } from "../../../../../services/auth/auth.service";
import GroupOwnerModel from "../../../../../model/groupOwner.model";
import { MEMBER_STATUS } from "../../../../../constants/status/status";


// export const emailVerificationWithPasswordChange = async (req: Request, res: Response): Promise<any> => {
// 	try {
// 		const { type, email, otp, password, confirmPassword, role } = req.body;
// 		// console.log("Request body:", req.body);

// 		// Validate required fields
// 		if (!email || !type) {
// 			return res.status(StatusCodes.BAD_REQUEST).json({
// 				message: MESSAGE.custom("Email or type is missing!")
// 			});
// 		}

// 		// Determine model based on role
// 		let model = getModelByRole(role);
// 		if (!model) {
// 			return res.status(StatusCodes.BAD_REQUEST).json({
// 				message: MESSAGE.custom("Unauthorized Role!")
// 			});
// 		}

// 		switch (type) {
// 			case "email":
// 				return await handleEmailVerification(email, model, role, res);

// 			case "otp":
// 				return await handleOtpVerification(email, otp, model, res);

// 			case "password":
// 				return await handlePasswordChange(email, password, confirmPassword, model, res);

// 			case "forgotemail":
// 				return await handleForgotEmail(email, model, res);

// 			default:
// 				return res.status(StatusCodes.BAD_REQUEST).json({
// 					message: MESSAGE.custom("Invalid request type!")
// 				});
// 		}
// 	} catch (error) {
// 		console.error("Error in email verification:", error);
// 		return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
// 			message: MESSAGE.custom("Email verification failed!"),
// 			error
// 		});
// 	}
// };

// // Helper function to get model based on role
// const getModelByRole = (role: string) => {
// 	if (role === ROLES.super_admin || role === ROLES.admin) {
// 		return GroupOwnerModel;
// 	} else if ([ROLES.competition_creator, ROLES.individual_voter, ROLES.participant].includes(role)) {
// 		return MemberModel;
// 	}
// 	return null;
// };

// // Handle email verification
// const handleEmailVerification = async (email: string, model: any, role: string, res: Response) => {
// 	const expiresAt = new Date(Date.now() + 5 * 60 * 1000);
// 	const generatedOtp = await service.common.generateOtp(4);
// 	const emailExists = await model.findOne({ email });

// 	if (emailExists) {
// 		return res.status(StatusCodes.NOT_FOUND).json({
// 			message: MESSAGE.custom("Email Not Registered")
// 		});
// 	}

// 	// Update or create OTP record
// 	const result = await model.findOneAndUpdate(
// 		{ email },
// 		{ otp: generatedOtp, expiresAt },
// 		{ new: true, upsert: true, runValidators: true }
// 	);

// 	return res.status(StatusCodes.OK).json({
// 		message: "OTP sent successfully!",
// 		result: { email: result.email, otp: result.otp, expiresAt: result.expiresAt }
// 	});
// };

// // Handle OTP verification
// const handleOtpVerification = async (email: string, otp: string, model: any, res: Response) => {
// 	const member = await model.findOne({ email });

// 	if (!member || !member.otp || !member.expiresAt || member.expiresAt < new Date()) {
// 		return res.status(StatusCodes.NOT_FOUND).json({
// 			message: MESSAGE.custom("OTP expired or invalid!")
// 		});
// 	}

// 	if (member.otp !== otp) {
// 		return res.status(StatusCodes.NOT_FOUND).json({
// 			message: MESSAGE.custom("Invalid OTP!")
// 		});
// 	}

// 	// Clear OTP after verification
// 	await model.findOneAndUpdate(
// 		{ email },
// 		{ is_verified: true, otp: null, expiresAt: null }
// 	);

// 	return res.status(StatusCodes.OK).json({
// 		message: MESSAGE.custom("OTP Verified Successfully!"),
// 		result: member
// 	});
// };

// // Handle password change
// const handlePasswordChange = async (email: string, password: string, confirmPassword: string, model: any, res: Response) => {
// 	if (!password || !confirmPassword) {
// 		return res.status(StatusCodes.BAD_REQUEST).json({
// 			message: MESSAGE.custom("Password and confirm password are required!")
// 		});
// 	}

// 	if (password !== confirmPassword) {
// 		return res.status(StatusCodes.BAD_REQUEST).json({
// 			message: MESSAGE.custom("Confirm password doesn't match!")
// 		});
// 	}

// 	// Update password
// 	await model.findOneAndUpdate(
// 		{ email },
// 		{ password: await service.auth.hashPassword(password) }
// 	);

// 	return res.status(StatusCodes.OK).json({
// 		message: MESSAGE.custom("Password changed successfully!")
// 	});
// };

// // Handle forgot email
// const handleForgotEmail = async (email: string, model: any, res: Response) => {
// 	const expiresAt = new Date(Date.now() + 5 * 60 * 1000);
// 	const generatedOtp = await service.common.generateOtp(4);
// 	const emailExists = await model.findOne({ email });

// 	if (emailExists) {
// 		const result = await model.findOneAndUpdate(
// 			{ email },
// 			{ otp: generatedOtp, expiresAt },
// 			{ new: true, upsert: true, runValidators: true }
// 		);
// 		return res.status(StatusCodes.OK).json({
// 			message: "OTP sent successfully!",
// 			result: { email: result.email, otp: result.otp, expiresAt: result.expiresAt }
// 		});
// 	} else {
// 		return res.status(StatusCodes.NOT_FOUND).json({
// 			message: MESSAGE.custom("Email not found!")
// 		});
// 	}
// };








export const emailVerificationWithPasswordChange = async (req: Request, res: Response): Promise<any> => {
	// try {
	const { type, email, otp, password, confirmPassword, role, forgotemail } = req.body;

	// Validate required fields
	if (!email || !type) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Email or type is missing!")
		});
	}

	let model: Model<any>;
	if (role === ROLES.super_admin || role === ROLES.admin) {
		model = GroupOwnerModel;
	} else if (role === ROLES.competition_creator || role === ROLES.individual_voter || role === ROLES.participant) {
		model = MemberModel;
	} else {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Unauthorised Role!")
		});
	}

	let isAdmin = false;

	switch (type) {
		case "email": {
			const expiresAt = new Date(Date.now() + 5 * 60 * 1000);
			const generatedOtp = await service.common.generateOtp(4);
			const isApprovedbyAdmin = role === ROLES.competition_creator ? MEMBER_STATUS.pending : MEMBER_STATUS.approved;
			// Check for user role
			if (!role) {
				// Check if the email exists in either model
				const emailExists = await MemberModel.findOne({ email, is_verified: true, is_registered: true }) ||
					await GroupOwnerModel.findOne({ email, is_registered: true });

				// Determine if the user is an admin
				isAdmin = !!emailExists && (emailExists as any).is_registered; // Type assertion added

				if (!emailExists) {
					return res.status(StatusCodes.NOT_FOUND).json({
						message: MESSAGE.custom("Email Not Registered")
					});
				}

				model = isAdmin ? GroupOwnerModel : MemberModel;
			} else {
				// Handle roles for ADMIN or SUPER ADMIN
				if (['ADMIN', 'SUPER ADMIN'].includes(role)) {
					const emailExists = await GroupOwnerModel.findOne({ email });

					if (!emailExists) {
						return res.status(StatusCodes.NOT_FOUND).json({
							message: MESSAGE.custom("Email Not Registered! Contact the Group Owner")
						});
					}
					if (emailExists.is_registered) {
						return res.status(StatusCodes.NOT_FOUND).json({
							message: MESSAGE.custom("Duplicate Email! User Already Exist")
						});
					}
					model = GroupOwnerModel;
				} else {
					// Handle other roles
					const emailExists = await MemberModel.findOne({ email, is_verified: true, is_registered: true });

					if (emailExists) {
						return res.status(StatusCodes.NOT_FOUND).json({
							message: MESSAGE.custom("Email Already Registered and Verified!")
						});
					}
					model = MemberModel;
				}
			}

			// Update or create a new OTP record
			const result = await model.findOneAndUpdate(
				{ email, role },
				{
					otp: generatedOtp, expiresAt, is_approved: isApprovedbyAdmin
				},
				{ new: true, upsert: true, runValidators: true }
			);

			return res.status(StatusCodes.OK).json({
				message: "OTP sent successfully!",
				result: { email: result.email, otp: result.otp, expiresAt: result.expiresAt }
			});
		}
		case "otp": {
			let member = await MemberModel.findOne({ email }) ||
				await GroupOwnerModel.findOne({ email });
			isAdmin = !!member && (member as any).is_registered;

			if (!member) {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("Email not found!")
				});
			}

			if (!member.otp || !member.expiresAt || member.expiresAt < new Date()) {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("OTP expired or invalid!")
				});
			}

			if (member.otp !== otp) {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("Invalid OTP!")
				});
			}

			// Clear OTP after verification
			await model.findOneAndUpdate(
				{ email },
				{ is_verified: isAdmin ? undefined : true, otp: null, expiresAt: null }
			);

			return res.status(StatusCodes.OK).json({
				message: MESSAGE.custom("OTP Verified Successfully!"),
				result: member
			});
		}

		case "password": {
			if (!password || !confirmPassword) {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("Password and confirm password are required!")
				});
			}

			if (password !== confirmPassword) {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("Confirm password doesn't match!")
				});
			}

			let member = await MemberModel.findOne({ email }) ||
				await GroupOwnerModel.findOne({ email });
			isAdmin = !!member && (member as any).is_registered;

			if (!member) {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("Email not found!")
				});
			}

			// Update password
			await model.findOneAndUpdate(
				{ email },
				{ password: await service.auth.hashPassword(password) }
			);

			return res.status(StatusCodes.OK).json({
				message: MESSAGE.custom("Password changed successfully!")
			});
		}

		case "forgotemail": {
			const expiresAt = new Date(Date.now() + 5 * 60 * 1000);
			const generatedOtp = await service.common.generateOtp(4);
			const emailExists = await model.findOne({ email });

			if (emailExists) {
				const result = await model.findOneAndUpdate(
					{ email },
					{ otp: generatedOtp, expiresAt },
					{ new: true, upsert: true, runValidators: true }
				);
				// Implement your forgot email logic here
				return res.status(StatusCodes.OK).json({
					message: "OTP sent successfully!",
					result: { email: result.email, otp: result.otp, expiresAt: result.expiresAt }
				});
			} else {
				return res.status(StatusCodes.NOT_FOUND).json({
					message: MESSAGE.custom("Email not found!")
				});
			}
		}

		default: {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.custom("Invalid request type!")
			});
		}
	}
	// } catch (error) {
	// 	console.error("Error in email verification:", error);
	// 	return res.status(StatusCodes.INTERNAL_SERVER_ERROR).json({
	// 		message: MESSAGE.custom("Email verification failed!"),
	// 		error
	// 	});
	// }
};
