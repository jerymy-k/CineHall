<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;

class PaimentController extends Controller
{
    public function pay(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        // Ensure the reservation belongs to the authenticated user
        if ($reservation->user_id !== $user->id) {
            return response()->json([
                'message' => 'You are not authorized to pay for this reservation.',
            ], 403);
        }

        // Prevent double payment — only pending reservations can be paid
        if ($reservation->status !== 'pending') {
            return response()->json([
                'message' => 'This reservation cannot be paid. Current status: ' . $reservation->status,
            ], 400);
        }

        // Check if expired
        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            $reservation->update(['status' => 'canceled']);
            return response()->json([
                'message' => 'Reservation expired. Cannot pay.',
            ], 400);
        }

        // Convert total_price to centimes (lowest currency unit for MAD)
        $amountInCentimes = (int) round($reservation->total_price * 100);

        try {
            // Create a Stripe PaymentIntent via Cashier's pay() method
            $payment = $user->pay($amountInCentimes);

            // Store the payment record in our database
            $paymentRecord = Payment::create([
                'reservation_id'          => $reservation->id,
                'user_id'                 => $user->id,
                'stripe_payment_intent_id' => $payment->id,
                'amount'                  => $amountInCentimes,
                'currency'                => config('cashier.currency', 'mad'),
                'status'                  => 'pending',
            ]);

            // Mark reservation as paid upon successful payment intent creation
            $reservation->update([
                'status' => 'paid',
                'expires_at' => null,
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

    public function ticket(Request $request, Reservation $reservation)
    {
        $user = $request->user();

        // Ensure the reservation belongs to the authenticated user
        if ($reservation->user_id !== $user->id) {
            return response()->json([
                'message' => 'You are not authorized to access this ticket.',
            ], 403);
        }

        // Only paid reservations get a ticket
        if ($reservation->status !== 'paid') {
            return response()->json([
                'message' => 'Ticket is only available for paid reservations. Current status: ' . $reservation->status,
            ], 400);
        }

        // Eager-load all needed relationships
        $reservation->load(['user', 'session.movie', 'session.room', 'reserved_seats']);

        // Generate QR code as inline SVG (pointing to the reservation endpoint)
        $qrUrl = url("/api/reservations/{$reservation->id}");
        $qrCode = QrCode::size(150)->generate($qrUrl);

        // Render the Blade view to PDF
        $pdf = Pdf::loadView('ticket', [
            'reservation' => $reservation,
            'qrCode'      => $qrCode,
        ])->setPaper('a4', 'portrait');

        $filename = "cinehall-ticket-reservation-{$reservation->id}.pdf";

        return $pdf->download($filename);
    }
}
