<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

use App\Http\Requests\StoreRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use function PHPUnit\Framework\isEmpty;



class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $users = User::where('is_admin', false)
            ->get();

        if ($users->isEmpty()) {
            return response()->json(['Error', 'User Not Found'], 404);
        }

        return response()->json([
            'data' => $users,
            'status' => 'success',
            'count' => $users->count()
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {

        $checkdata = $request->validated();

        if ($checkdata) {
            $user = User::create([
                'first_name' => $checkdata['first_name'],
                'last_name' => $checkdata['last_name'],
                'email' => $checkdata['email'],
                'password' => Hash::make($checkdata['password']),
            ]);
        }

        if ($user) {
            $token = JWTAuth::fromUser($user);
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token,
                'type' => 'bearer',
            ], 201);
        }

        return response()->json(['Error' => 'Something Wrong Try Again'], 500);
    }

    public function login(Request $request)
    {
        $user = $request->only('email', 'password');
        $token = JWTAuth::attempt($user);
        if (!$token) {
            return response()->json(['Error', 'data Invalid'], 500);
        }

        return response()->json([
            'message' => 'login success',
            'token' => $token,
            'token_type' => 'bearer'
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'string|min:3|max:70',
            'last_name' => 'string|min:3|max:70',
        ]);

        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['Error' => 'Unauthorized'], 401);
        }

        $update = $user->update($validatedData);
        if ($update) {
            return response()->json([
                'status' => 'success',
            ], 200);
        }

        return response()->json(['Message' => 'User Not Found Or Data Invalid'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function ban(string $id)
    {
        $user = User::where('id', $id)
            ->where('is_admin', false)
            ->first();

        if (!$user) {
            return response()->json(['Error' => 'User Not Found'], 404);
        }

        if (!$user->is_active) {
            return response()->json(['Error' => 'User Already Banned'], 500);
        }

        $user->is_active = false;
        $user->save();

        return response()->json(['Message' => 'User Banned Success'], 200);
    }
    public function unban(string $id)
    {
        $user = User::where('id', $id)
            ->where('is_admin', false)
            ->first();

        if (!$user) {
            return response()->json(['Error' => 'User Not Found'], 404);
        }

        if ($user->is_active) {
            return response()->json(['Error' => 'User Already UnBanned'], 500);
        }

        $user->is_active = true;
        $user->save();

        return response()->json(['Message' => 'User Unbanned Success'], 200);
    }
}
