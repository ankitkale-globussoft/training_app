<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:6|max:32',
            'user_type'   => 'required|in:user,admin',
            'phone'       => 'nullable|string|max:10|unique:users,phone',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);

        if ($request->hasFile('profile_pic')) {
            $validated['profile_pic'] = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        $user = User::create($validated);

        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'success' => true,
            'result'  => ['token' => $token],
            'msg'     => 'User signup successful'
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }

        $login = $request->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (!Auth::attempt([$fieldType => $login, 'password' => $request->password])) {
            return response()->json([
                'success' => false,
                'msg'     => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json([
            'success' => true,
            'result'  => ['token' => $token, 'user' => $user],
            'msg'     => 'Login successful'
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|string|max:255',
            'email'       => 'nullable|email|unique:users,email,' . $user->id,
            'phone'       => 'nullable|string|max:10',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }

        // Update only provided fields
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('profile_pic')) {
            // Delete old profile pic if exists
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }
            
            // Store new profile pic
            $user->profile_pic = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        $user->save();

        return response()->json([
            'success' => true,
            'result'  => [
                'user' => [
                    'id'          => $user->id,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'phone'       => $user->phone,
                    'user_type'   => $user->user_type,
                    'profile_pic' => $user->profile_pic ? asset('storage/' . $user->profile_pic) : null
                ]
            ],
            'msg' => 'Profile updated successfully'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:6|max:32|different:current_password',
            'confirm_password' => 'required|string|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'msg'     => 'Current password is incorrect'
            ], 401);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Optional: Revoke all tokens (logout from all devices)
        // $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'msg'     => 'Password reset successfully'
        ], 200);
    }

}