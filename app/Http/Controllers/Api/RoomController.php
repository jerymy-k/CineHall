<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/rooms',
        summary: 'Get all rooms',
        tags: ['Rooms'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'List of rooms')
        ]
    )]
    public function index()
    {
        $rooms = Room::all();

        return response()->json([
            'status' => 'success',
            'rooms' => $rooms
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: '/rooms',
        summary: 'Create room',
        tags: ['Rooms'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'total_seats', 'is_vip'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'total_seats', type: 'integer'),
                    new OA\Property(property: 'is_vip', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Room created')
        ]
    )]
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:1|max:255',
            'total_seats' => 'required|integer|min:10|max:1000',
            'is_vip' => 'boolean',
        ]);

        $room = Room::create($validatedData);

        return response()->json(['message' => 'Room created successfully', 'room' => $room], 201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: '/rooms/{id}',
        summary: 'Get room by ID',
        tags: ['Rooms'],
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
            new OA\Response(response: 200, description: 'Room details')
        ]
    )]
    public function show(string $id)
    {
        $room = Room::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'room' => $room
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: '/rooms/{id}',
        summary: 'Update room',
        tags: ['Rooms'],
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
            new OA\Response(response: 200, description: 'Room updated')
        ]
    )]
    public function update(Request $request, string $id)
    {
        $room = Room::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'string|min:1|max:255',
            'total_seats' => 'integer|min:10|max:1000',
            'is_vip' => 'boolean',
        ]);

        $room->update($validatedData);

        return response()->json(['message' => 'Room updated successfully', 'room' => $room]);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: '/rooms/{id}',
        summary: 'Delete room',
        tags: ['Rooms'],
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
            new OA\Response(response: 200, description: 'Room deleted')
        ]
    )]
    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully']);
    }
}

