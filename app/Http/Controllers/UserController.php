<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{

    public function roles() {
        return response()->json(UserRole::all());
    }

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
            'role'=>$role->user_role,
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

    // Fetch drivers for users with role = 4
    public function fetchDrivers(Request $request)
    {
        // Fetch users with role_id = 4 (assuming role 4 is for drivers)
        $drivers = User::where('user_role_id', 4)->get();

        // Check if there are drivers available
        if ($drivers->isEmpty()) {
            return response()->json([
                'message' => 'No drivers found.',
            ], 404);
        }

        // Log the fetched drivers for debugging
        Log::info('Fetched Drivers:', ['drivers' => $drivers]);

        // Return the list of drivers
        return response()->json([
            'drivers' => $drivers,
        ]);
    }
    public function getUsers()
    {
        // Get the currently authenticated user
        $authUser = Auth::user();
        
        // Get all users excluding the authenticated user
        $users = User::join('user_roles', 'users.user_role_id', '=', 'user_roles.id')
    ->where('users.id', '!=', $authUser->id)
    ->get(['users.id', 'users.name', 'users.email', 'user_roles.user_role as role']);


        // Return the list of users with their role
        return response()->json($users);
    }

     // Create a new user
     public function createUser(Request $request)
     {
         // Validate the input data
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|unique:users,email',
             'role' => 'required|integer',
         ]);
         
         $password = 'password@1234';
         // Create a new user
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => $password,
             'user_role_id' => $request->role,
         ]);
 
         // Return the newly created user
         return response()->json($user, 201);
     }

 
     // Update an existing user
     public function updateUser(Request $request, $id)
     {
         // Validate the input data
         $request->validate([
             'name' => 'required|string|max:255',
             'role' => 'required|integer',
         ]);
 
         // Find the user by ID
         $user = User::findOrFail($id);
 
         // Update user details
         $user->update([
             'name' => $request->name,
             'user_role_id' => $request->role,
         ]);
 
         // Return the updated user
         return response()->json($user, 200);
     }
 
     public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => [
                'nullable',
                'min:6',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'confirmed'
            ]
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one symbol, and one number.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);
        

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }
     // Delete an existing user
     public function deleteUser($id)
     {
         // Find the user by ID
         $user = User::findOrFail($id);
 
         // Delete the user
         $user->delete();
 
         // Return a success message
         return response()->json(['message' => 'User deleted successfully'], 200);
     }
}
