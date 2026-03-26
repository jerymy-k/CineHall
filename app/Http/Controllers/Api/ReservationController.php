<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\ReservedSeat;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class ReservationController extends Controller
{
    /**
     * Display a listing of the user's reservations.
     */
    #[OA\Get(
        path: '/reservations',
        summary: 'Get user reservations',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of reservations')
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['session.movie', 'session.room', 'reserved_seats', 'payment'])
            ->valid()
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'reservations' => $reservations
        ]);
    }

    /**
     * Store a new reservation with selected seats.
     */
    #[OA\Post(
        path: '/reservations',
        summary: 'Create reservation',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['session_id', 'seat_numbers'],
                properties: [
                    new OA\Property(property: 'session_id', type: 'integer'),
                    new OA\Property(property: 'seat_numbers', type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Reservation created')
        ]
    )]
    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'session_id' => 'required|exists:movie_sessions,id',
            'seat_numbers' => 'required|array|min:1|max:10',
            'seat_numbers.*' => 'integer|min:1',
        ]);

        $session = Session::with('room')->findOrFail($validated['session_id']);

        DB::beginTransaction();

        try {
            // Check availability
            $currentReserved = ReservedSeat::whereHas('reservation', function ($q) use ($session) {
                $q->valid();
            })->whereIn('seat_number', $validated['seat_numbers'])->count();

            if ($currentReserved > 0) {
                throw new \Exception('Some seats are already reserved');
            }

            $isVip = $session->room->is_vip || $session->type === 'vip';
            if ($isVip) {
                $seatCount = count($validated['seat_numbers']);
                if ($seatCount % 2 !== 0) {
                    throw new \Exception('VIP sessions require even number of seats (couples)');
                }
                // Optional: check if pairs are sequential
                sort($validated['seat_numbers']);
                for ($i = 0; $i < $seatCount; $i += 2) {
                    if ($validated['seat_numbers'][$i+1] - $validated['seat_numbers'][$i] !== 1) {
                        throw new \Exception('VIP couple seats must be sequential pairs');
                    }
                }
            }

            $totalPrice = $session->price * count($validated['seat_numbers']);

            $reservation = Reservation::create([
                'user_id' => $user->id,
                'session_id' => $session->id,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'expires_at' => Carbon::now()->addMinutes(15),
            ]);

            foreach ($validated['seat_numbers'] as $seatNumber) {
                ReservedSeat::create([
                    'reservation_id' => $reservation->id,
                    'seat_number' => $seatNumber,
                ]);
            }

            $reservation->load(['session.movie', 'session.room', 'reserved_seats']);

            DB::commit();

            return response()->json([
                'message' => 'Reservation created successfully. Pay within 15 minutes.',
                'reservation' => $reservation,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Reservation failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/reservations/{id}',
        summary: 'Get reservation by ID',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reservation details')
        ]
    )]
    public function show(string $id, Request $request)
    {
        $user = $request->user();
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['session.movie', 'session.room', 'reserved_seats', 'payment'])
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation
        ]);
    }

    /**
     * Cancel or modify reservation (only pending).
     */
    #[OA\Put(
        path: '/reservations/{id}',
        summary: 'Cancel or modify reservation',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reservation cancelled')
        ]
    )]
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($reservation->expires_at && $reservation->expires_at->isPast()) {
            $reservation->update(['status' => 'canceled']);
            return response()->json(['message' => 'Reservation expired and cancelled']);
        }

        $reservation->update(['status' => 'canceled']);

        return response()->json(['message' => 'Reservation cancelled successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/reservations/{id}',
        summary: 'Delete reservation',
        tags: ['Reservations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reservation deleted')
        ]
    )]
    public function destroy(string $id, Request $request)
    {
        $user = $request->user();
        $reservation = Reservation::where('id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted successfully']);
    }
}

