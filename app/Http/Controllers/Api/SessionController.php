<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Movie;
use App\Models\Room;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/sessions',
        summary: 'Get all sessions',
        tags: ['Sessions'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of sessions')
        ]
    )]
    public function index()
    {
        $sessions = Session::with(['movie', 'room'])->get();

        return response()->json([
            'status' => 'success',
            'sessions' => $sessions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: '/sessions',
        summary: 'Create session',
        tags: ['Sessions'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['start_at', 'end_at', 'language', 'price', 'room_id', 'movie_id'],
                properties: [
                    new OA\Property(property: 'start_at', type: 'string', format: 'datetime'),
                    new OA\Property(property: 'end_at', type: 'string', format: 'datetime'),
                    new OA\Property(property: 'language', type: 'string'),
                    new OA\Property(property: 'price', type: 'number'),
                    new OA\Property(property: 'room_id', type: 'integer'),
                    new OA\Property(property: 'movie_id', type: 'integer'),
                    new OA\Property(property: 'type', type: 'string', enum: ['normal', 'vip']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Session created')
        ]
    )]
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'start_at' => 'required|date|after:now',
            'end_at' => 'required|date|after:start_at',
            'language' => 'required|in:en,zh,hi,es,fr,ar,bn,pt,ru,ur,de',
            'price' => 'required|numeric|min:0',
            'room_id' => 'required|exists:rooms,id',
            'movie_id' => 'required|exists:movies,id',
            'type' => 'in:normal,vip',
        ]);

        $session = Session::create($validatedData);

        return response()->json(['message' => 'Session created successfully', 'session' => $session->load(['movie', 'room'])], 201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/sessions/{id}',
        summary: 'Get session by ID',
        tags: ['Sessions'],
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
            new OA\Response(response: 200, description: 'Session details')
        ]
    )]
    public function show(string $id)
    {
        $session = Session::with(['movie', 'room'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'session' => $session
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: '/sessions/{id}',
        summary: 'Update session',
        tags: ['Sessions'],
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
            new OA\Response(response: 200, description: 'Session updated')
        ]
    )]
    public function update(Request $request, string $id)
    {
        $session = Session::findOrFail($id);

        $validatedData = $request->validate([
            'start_at' => 'date|after:now',
            'end_at' => 'date|after:start_at',
            'language' => 'in:en,zh,hi,es,fr,ar,bn,pt,ru,ur,de',
            'price' => 'numeric|min:0',
            'room_id' => 'exists:rooms,id',
            'movie_id' => 'exists:movies,id',
            'type' => 'in:normal,vip',
        ]);

        $session->update($validatedData);

        return response()->json(['message' => 'Session updated successfully', 'session' => $session->load(['movie', 'room'])]);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/sessions/{id}',
        summary: 'Delete session',
        tags: ['Sessions'],
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
            new OA\Response(response: 200, description: 'Session deleted')
        ]
    )]
    public function destroy(string $id)
    {
        $session = Session::findOrFail($id);
        $session->delete();

        return response()->json(['message' => 'Session deleted successfully']);
    }

    /**
     * Get available seats for session
     */
    #[OA\Get(
        path: '/sessions/{id}/available-seats',
        summary: 'Get available and taken seats for session',
        tags: ['Sessions'],
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
            new OA\Response(response: 200, description: 'Seats status')
        ]
    )]
    public function availableSeats(string $id)
    {
        $session = Session::with('room')->findOrFail($id);

        $totalSeats = $session->room->total_seats;

        $reservedSeats = $session->reservations()->valid()->with('reserved_seats')->get()
            ->pluck('reserved_seats')
            ->flatten()
            ->pluck('seat_number')
            ->unique()
            ->toArray();

        $availableSeats = [];
        $takenSeats = $reservedSeats;

        for ($i = 1; $i <= $totalSeats; $i++) {
            if (!in_array($i, $reservedSeats)) {
                $availableSeats[] = $i;
            }
        }

        $isVip = $session->room->is_vip || $session->type === 'vip';

        return response()->json([
            'session_id' => $id,
            'total_seats' => $totalSeats,
            'available_seats' => $availableSeats,
            'taken_seats' => $takenSeats,
            'is_vip' => $isVip,
            'vip_couple_required' => $isVip,
        ]);
    }
}

