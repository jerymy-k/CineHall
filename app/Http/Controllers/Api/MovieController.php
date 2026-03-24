<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Cache\Repository;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/movies",
        summary: "Get all movies",
        tags: ["Movies"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "List of movies")
        ]
    )]
    public function index()
    {

        $movies = Movie::all();

        return response()->json([
            'status' => 'success',
            'movies' => $movies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: "/movies",
        summary: "Create movie",
        tags: ["Movies"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "description", "duration", "min_age", "image", "trailer"],
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "duration", type: "integer"),
                    new OA\Property(property: "min_age", type: "integer"),
                    new OA\Property(property: "image", type: "string"),
                    new OA\Property(property: "trailer", type: "string"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Movie created")
        ]
    )]
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|min:1|max:255',
            'description' => 'required|string|min:10',
            'image' => 'url|nullable',
            'duration' => 'required|integer|min:1|max:500',
            'min_age' => 'required|integer|min:0|max:100',
            'trailer' => 'nullable|url',
        ]);

        Movie::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'image' => $validatedData['image'],
            'duration' => $validatedData['duration'],
            'min_age' => $validatedData['min_age'],
            'trailer' => $validatedData['trailer'],
        ]);

        return response()->json(['Message' => 'Creation is Successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: "/movies/{id}",
        summary: "Get movie by ID",
        tags: ["Movies"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Movie details")
        ]
    )]
    public function show(string $id)
    {
        $movie = Movie::where('id', $id)->first();

        if ($movie) {
            return response()->json([
                'movie' => $movie
            ], 200);
        }

        return response()->json(['Error' => 'Movie Not Found']);
    }

    /**
     * Update the specified resource in storage.
     */

    #[OA\Put(
        path: "/movies/{id}",
        summary: "Update movie",
        tags: ["Movies"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Movie updated")
        ]
    )]
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|min:1|max:255',
            'description' => 'required|string|min:10',
            'image' => 'url|nullable',
            'duration' => 'required|integer|min:1|max:500',
            'min_age' => 'required|integer|min:0|max:100',
            'trailer' => 'nullable|url',
        ]);

        $movie = Movie::where('id', $id)->first();

        if (!$movie) {
            return response()->json(['Error' => 'Movie Not Found'], 404);
        }

        $movie->update($validatedData);


        return response()->json(['message' => 'Movie Updated Success'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    #[OA\Delete(
        path: "/movies/{id}",
        summary: "Delete movie",
        tags: ["Movies"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Movie deleted")
        ]
    )]
    public function destroy(string $id)
    {
        $movie = Movie::where('id', $id)->delete();

        if ($movie) {
            return response()->json(['Message' => 'Movie Deleted Successfully'], 200);
        }
        return response()->json(['Error' => 'Movie Not Found'], 404);
    }
}
