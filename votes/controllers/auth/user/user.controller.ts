import { Request, Response } from "express";
import { Model } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../../constants/message";
import { ROLES } from "../../../../../constants/roles/roles";
import service from "../../../../../services";
import GroupOwnerModel from "../../../../../model/groupOwner.model";
import MemberModel from "../../../../../model/member.model";
import { capitalizeString, getDetailsByEmail } from "../../../../../utils/helper";
import formatter from "../../../../../utils/formatter";

export const changePassword = async (req: Request, res: Response): Promise<any> => {
	try {
		const {
			email,
			old_password,
			new_password,
			confirm_password
		} = req.body;
		let user = await GroupOwnerModel.findOne({ email });
		if (!user) {
			user = await MemberModel.findOne({ email });
		}
		if (!user) {
			return res.status(404).json({ message: 'User not found' });
		}
		const oldPassword: string = user.password ?? '';

		const isMatch = await service.auth.comparePassword(old_password, oldPassword);
		if (!isMatch) {
			return res.status(401).json({ message: 'Old password is incorrect' });
		}
		if (new_password !== confirm_password) {
			return res.status(400).json({ message: 'New passwords do not match' });
		}
		user.password = await service.auth.hashPassword(new_password); // This will be hashed in the pre-save hook
		await user.save();
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Something Went Wrong!"),
			error
		});
	}
};
export const getProfile = async (req: Request, res: Response): Promise<any> => {
	try {
		let role = req.user.role;
		let model: Model<any>,
			jwtPayload = {};

		if (role === ROLES.super_admin || role === ROLES.admin) {
			model = GroupOwnerModel;
		} else if (role === ROLES.member || role === ROLES.competition_creator || role === ROLES.individual_voter || role === ROLES.participant) {
			model = MemberModel;
		} else {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Unauthorised Role!")
			});
		}
		const userDetails = await model.findOne({
			email: req.user.email
		});

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.get.succ,
			result: {
				userDetails
			}
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Something went wrong!"),
			error
		});
	}
};
export const updateProfile = async (req: Request, res: Response): Promise<any> => {
	try {
		let role = req.user.role;
		let model: Model<any>,
			jwtPayload = {};

		if (role === ROLES.super_admin || role === ROLES.admin) {
			model = GroupOwnerModel;
		} else if (role === ROLES.member || role === ROLES.competition_creator || role === ROLES.individual_voter || role === ROLES.participant) {
			model = MemberModel;
		} else {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Unauthorised Role!")
			});
		}

		const userDetails = await model.findOne({
			email: req.user.email
		});
		if (!userDetails) {
			return res.status(StatusCodes.NOT_FOUND).json({
				message: MESSAGE.custom("User not found!")
			});
		}

		const user = await model.findByIdAndUpdate(userDetails._id, req.body);

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Profile updated successfully!"),
			Result: user
		});
	} catch (error: any) {
		console.log(error.message);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Profile updated unsuccessful!"),
			error
		});
	}
};
export const userList = async (req: Request, res: Response): Promise<any> => {
	try {
		const { role } = req.body
		let model: Model<any>,
			jwtPayload = {};

		if (role === ROLES.super_admin || role === ROLES.admin) {
			model = GroupOwnerModel;
		} else if (role === ROLES.member || role === ROLES.competition_creator || role === ROLES.individual_voter || role === ROLES.participant) {
			model = MemberModel;
		} else {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Unauthorised Role!")
			});
		}
		const users = await model.find({ role, is_registered: true, is_verified: true });
		let userResponses = users;
		if (role === ROLES.super_admin || role === ROLES.admin) {
			userResponses = await formatter.formatGroupOwnersResponse(users)
		} else if (role === ROLES.member) {
			userResponses = await formatter.formatMembersResponse(users)
		} else if (role === ROLES.participant) {
			userResponses = await formatter.formatParticipantsResponse(users)
		} else if (role === ROLES.competition_creator) {
			userResponses = await formatter.formatCompetitionCreatorsResponse(users)
		} else if (role === ROLES.individual_voter) {
			userResponses = await formatter.formatIndividualVotersResponse(users)
		}
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom(await capitalizeString(role) + " List fetch successfully!"),
			users: userResponses
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Something went wrong!"),
			error
		});
	}
};
export const userDetails = async (req: Request, res: Response): Promise<any> => {
	try {
		const { email } = req.body;
		let user = await GroupOwnerModel.findOne({ email });
		if (!user) {
			user = await MemberModel.findOne({ email: email, is_registered: true, is_verified: true });
		}
		if (!user) {
			return res.status(404).json({ message: 'User not found' });
		}

		const role = user.role;
		let userResponse: any = {};

		if (user instanceof GroupOwnerModel) {
			if (role === ROLES.super_admin || role === ROLES.admin) {
				userResponse = await formatter.formatGroupOwnerResponse(user);
			}
		} else if (user instanceof MemberModel) {
			if (role === ROLES.member) {
				userResponse = await formatter.formatMemberResponse(user);
			} else if (role === ROLES.participant) {
				userResponse = await formatter.formatParticipantResponse(user);
			} else if (role === ROLES.competition_creator) {
				userResponse = await formatter.formatCompetitionCreatorResponse(user);
			} else if (role === ROLES.individual_voter) {
				userResponse = await formatter.formatIndividualVoterResponse(user);
			}
		}

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Details fetch successfully!"),
			users: userResponse
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Something went wrong!"),
			error
		});
	}
};
export const deleteUser = async (req: Request, res: Response): Promise<any> => {
	try {
		const { email } = req.body;

		let user = await GroupOwnerModel.findOne({ email });
		if (!user) {
			user = await MemberModel.findOne({ email });
		}
		if (!user) {
			return res.status(404).json({ message: 'User not found' });
		}
		let model: Model<any> | null = null;
		if (user.role === ROLES.super_admin || user.role === ROLES.admin) {
			model = GroupOwnerModel;
		} else if (user.role === ROLES.member || user.role === ROLES.competition_creator || user.role === ROLES.individual_voter || user.role === ROLES.participant) {
			model = MemberModel;
		}
		const deletedUser = (model) ? await model.findByIdAndDelete(user._id) : null;
		if (!deletedUser) return res.status(404).json({ message: 'Something went wrong' });

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Deleted successfully!")
		});
	} catch (error) {
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Something went wrong!"),
			error
		});
	}
};