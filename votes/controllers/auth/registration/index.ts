import { adminRegistration, addAdmin, listAdmin } from "../registration/adminRegistration/adminregistration.controller";
import { superAdminRegistration } from "../registration/superAdminRegistration/superAdminRegistration.controller";
import { makeMemberRoleUpgradeRequest, memberRegistration } from "./memberRegistration/memberRegistration.controller";
import { emailVerificationWithPasswordChange } from "./verificationDetails.controller";

const registration = {
	superAdminRegistration,
	adminRegistration,
	memberRegistration,
	addAdmin,
	listAdmin,
	emailVerificationWithPasswordChange,
	makeMemberRoleUpgradeRequest
};
export default registration;
