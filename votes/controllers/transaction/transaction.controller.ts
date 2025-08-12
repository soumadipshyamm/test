import { Request, Response } from "express";
import { Model, ObjectId } from "mongoose";
import { StatusCodes } from "http-status-codes";
import { STRIPE_SECRET_KEY } from "../../../../config/config";
import { transaction, transactionStatusUpdate } from "../../../../services/transaction/transaction.service";
import { MESSAGE } from "../../../../constants/message";
import transactionModel from "../../../../model/transaction.model";
import { createStripePaymentIntents, verifyPaymentViaStripe } from "../../../../services/payment/stripe.service";
const stripe = require("stripe")(STRIPE_SECRET_KEY);

export const receivedPaymentDeatils = async (req: Request, res: Response): Promise<any> => {
	try {
		const {
			payment_intent_id,
			member_id,
			amount,
			payment_status,
			payment_type,
			status,
			message,
			transaction_date,
			respons_data
		} = req.body;

		const transactionPayload = {
			payment_intent_id: payment_intent_id,
			member_id: member_id,
			amount: amount,
			payment_status: payment_status,
			payment_type: payment_type,
			status: status,
			message: message,
			transaction_date: transaction_date,
			respons_data: respons_data
		};
		let data = await transaction(transactionPayload);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Subscription fetch successfully!"),
			data: data
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Subscription creation unsuccessful!"),
			error
		});
	}
};

export const transactionList = async (req: Request, res: Response): Promise<any> => {
	try {
		const member_id = req.params.id;
		const data = await transactionModel.find({ member_objectId: member_id }).sort({ createdAt: -1 });
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Transaction fetch successfully!"),
			data: data
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Transaction fetch unsuccessful!"),
			error
		});
	}
};
export const transactionDeatils = async (req: Request, res: Response): Promise<any> => {
	try {
		const { member_id } = req.body;
		const data = await transactionModel.find({ member_objectId: member_id }).sort({ createdAt: -1 });
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Transaction fetch successfully!"),
			data: data
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Transaction fetch unsuccessful!"),
			error
		});
	}
};

export const transactionUpdate = async (req: Request, res: Response): Promise<any> => {
	try {
		const { id, payment_status } = req.body;
		const transactionPayload = {
			id: id,
			payment_status: payment_status
		};
		let data = await transactionStatusUpdate(transactionPayload);

		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Transaction fetch successfully!"),
			data: data
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Transaction fetch unsuccessful!"),
			error
		});
	}
};

export const verifyPayment = async (req: Request, res: Response): Promise<any> => {
	const { id } = req.params;
	const { status, member_id, paymentIntent_id, payment_respons } = req.body;

	if (!paymentIntent_id) {
		return res.status(StatusCodes.UNPROCESSABLE_ENTITY).json({
			status: false,
			message: "Stripe order ID is required",
			data: {}
		});
	}

	const transactionPayload = {
		id,
		payment_status: status,
		respons_data: payment_respons || {}
	};

	try {
		// Retrieve the Payment Intent from Stripe
		// const paymentIntent = await stripe.paymentIntents.retrieve(paymentIntent_id);
		const paymentIntent = await verifyPaymentViaStripe(paymentIntent_id);

		if (paymentIntent && paymentIntent.status === "succeeded") {
			const data = await transactionStatusUpdate(transactionPayload);

			if (data?.payment_status === "SUCCESS") {
				return res.status(StatusCodes.OK).json({
					message: MESSAGE.custom("Transaction fetched successfully!"),
					result: data
				});
			}
			return res.status(StatusCodes.OK).json({
				message: MESSAGE.custom("Subscription successful!"),
				result: data
			});
		} else {
			return res.status(StatusCodes.BAD_REQUEST).json({
				message: MESSAGE.custom("Payment not successful!"),
				data: {}
			});
		}
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Subscription fetch unsuccessful!"),
			error
		});
	}
};

export const createPayment = async (req: Request, res: Response): Promise<any> => {
	try {
		const { member_id, competition_id, amount } = req.body;

		const paymentPayload = {
			member_id: member_id,
			competition_id: competition_id,
			amount: amount
		};
		// let paymentIdString: string = "";
		// memberId, competitionId, amount, type='card', status = "PENDING", transaction_status, message
		let stripePayment = await createStripePaymentIntents(
			member_id,
			competition_id,
			amount,
			"card",
			"PENDING",
			"CREDITED",
			"Join Competition"
		);
		return res.status(StatusCodes.OK).json({
			message: MESSAGE.custom("Transaction fetch successfully!"),
			data: { stripePayment, paymentPayload }
		});
	} catch (error) {
		console.log(error);
		return res.status(StatusCodes.BAD_REQUEST).json({
			message: MESSAGE.custom("Transaction fetch unsuccessful!"),
			error
		});
	}
};


