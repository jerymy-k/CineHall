<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'start_at' => 'required|date|after:now',
            'end_at' => 'required|date|after:start_at',
            'language' => 'required|string|in:en,zh,hi,es,fr,ar,bn,pt,ru,ur,de',
            'price' => 'required|numeric|min:0',
        ]);

        $checkDate = Session::where('room_id', $request->room_id)
            ->where('start_at', '<', $validateData['end_at'])
            ->where('end_at', '>', $validateData['start_at'])
            ->first();

        if (!$checkDate) {


            Session::create([
                'start_at' => $validateData['start_at'],
                'end_at' => $validateData['end_at'],
                'language' => $validateData['language'],
                'price' => $validateData['price'],
                'room_id' => $request->room_id,
                'movie_id' => $request->movie_id
            ]);
            return response()->json(['message' => 'session stored successfully'], 201);
        }


        return response()->json(['Error' => 'Is not Empty'], 500);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $session = Session::where('id', $id)->first();

        if (!$session) {
            return response()->json(['Error' => 'Session Not found'], 404);
        }
        return response()->json(['status' => 'Successfull', 'session' => $session], 200);
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
