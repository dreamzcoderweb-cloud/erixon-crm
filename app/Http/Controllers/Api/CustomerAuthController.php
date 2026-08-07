<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20', 'unique:customers,mobile'],
            'password' => ['required', 'string', 'min:6'],
            'reference_code' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'password' => Hash::make($validated['password']),
            'reference_code' => $validated['reference_code'] ?? null,
        ]);

        $token = $customer->createToken('customer-api')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'token' => $token,
            'customer' => $customer,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::where('mobile', $validated['mobile'])->first();

        if (! $customer || ! Hash::check($validated['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'mobile' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $customer->createToken('customer-api')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'customer' => $customer,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'customer' => $request->user(),
        ]);
    }

    public function profile(){
        
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}

