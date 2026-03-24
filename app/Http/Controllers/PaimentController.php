<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Exception;

class PaimentController extends Controller
{
    public function pay(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        if ($reservation->user_id !== $user->id) {
            return response()->json([
                'message' => 'You are not authorized to pay for this reservation.',
            ], 403);
        }

        if ($reservation->status !== 'pending') {
            return response()->json([
                'message' => 'This reservation cannot be paid. Current status: ' . $reservation->status,
            ], 400);
        }

        $amountInCentimes = (int) round($reservation->total_price * 100);

        try {
            $payment = $user->pay($amountInCentimes);

            $paymentRecord = Payment::create([
                'reservation_id'          => $reservation->id,
                'user_id'                 => $user->id,
                'stripe_payment_intent_id' => $payment->id,
                'amount'                  => $amountInCentimes,
                'currency'                => config('cashier.currency', 'mad'),
                'status'                  => 'pending',
            ]);

            return response()->json([
                'message'       => 'Payment intent created successfully.',
                'client_secret' => $payment->client_secret,
                'payment'       => $paymentRecord,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Payment failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
