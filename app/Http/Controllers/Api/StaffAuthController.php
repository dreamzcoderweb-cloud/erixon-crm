<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('staff-api')->plainTextToken;

        $roleName = $user->getRoleNames()->first();
        $userData = $user->toArray();
        unset($userData['roles']);
        $userData['role'] = $roleName;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'user' => $userData,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $roleName = $user->getRoleNames()->first();
        $userData = $user->toArray();
        unset($userData['roles']);
        $userData['role'] = $roleName;

        return response()->json([
            'user' => $userData,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}

