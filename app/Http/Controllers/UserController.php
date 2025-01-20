<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Get the authenticated user
        $user = Auth::user();
        

        // Get the role for the user
        $role = $user->userRole;

        // Log the role information for debugging
        Log::info('Access Levels:', ['role' => $role]);

        if (!$role) {
            return response()->json([
                'message' => 'Role not found for the user.',
            ], 404);
        }
        // Log the role information for debugging
        Log::info('Access Levels:', ['role' => $role->access_level]);

        // Generate token
        $token = $user->createToken('auth_token', ['role' => $role->access_level])->plainTextToken;

        // Return response
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    // Method to get user profile data
    public function profile(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Return user data
        return response()->json([
            'username' => $user->name,  // Assuming the username is stored in the 'name' field
            'email' => $user->email,    // You can return more user info here if needed
        ]);
    }
}
