import { Request, Response } from "express";
import { StatusCodes } from "http-status-codes";
import { MESSAGE } from "../../../../constants/message";
import CompetitionModel from "../../../../model/competition.model";
import cron from 'node-cron';
import { getDetailsByEmail } from "../../../../utils/helper";
import VotePackageModel from "../../../../model/votePackage.model";
import { formatCompetitionResponse, formatCompetitionsResponse, formatAllCompetitionsResponse } from "../../../../utils/formatter/competitionResponseFormatter";
import SavedCompetitionModel from "../../../../model/savedCompetition.model";
import VoteModel from "../../../../model/vote.model";
import ParticipantModel from "../../../../model/participant.model";
import { PARTICIPATION_STATUS, COMPETITION_STATUS, MEMBER_STATUS } from "../../../../constants/status/status";
import VotingSubscriptionModel from "../../../../model/votingSubscription.model";
import transactionModel from "../../../../model/transaction.model";
import { roles, ROLES } from "../../../../constants/roles/roles";
import GroupOwnerModel from "../../../../model/groupOwner.model";
import MemberModel from "../../../../model/member.model";
import { cp } from "fs";



