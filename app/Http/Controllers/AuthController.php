<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        return $request->user();
    }

    // register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully created user!',
        ], 201);
    }

    // login and get token
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials',
            ], 401);
        }

        $user = $request->user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Successfully logged in!',
            'token' => $token,
        ]);
    }

    // logout
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out!',
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'password' => 'nullable|min:8',
            'password_confirmation' => 'nullable|same:password',
        ]);

        $authUser = $request->user();
        $user = User::find($authUser->id);

        if ($request->password) {
            $request->merge(['password' => bcrypt($request->password)]);
            $user->update($request->only('name', 'password'));
        } else {
            $user->update($request->only('name'));
        }

        return response()->json([
            'message' => 'Successfully updated profile!',
        ]);
    }
}
