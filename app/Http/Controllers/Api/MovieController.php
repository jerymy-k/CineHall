<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        if ($validatedData) {

            $user = auth('api')->user();
            if ($user->is_admin) {

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
        }
        return response()->json(['Error' => 'Something Wrong'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
