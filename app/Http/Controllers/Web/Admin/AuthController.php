<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function show_login()
    {
        return view('admin.login');
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

        $user = User::where($fieldType, $login)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'login' => ['Invalid credentials.']
                ],
                'msg' => 'Login failed'
            ], 422);
        }

        $remember = (bool) $request->remember;
        Auth::guard('web')->login($user, $remember);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'msg' => 'Login successful!',
            'redirect' => route('admin.dashboard')
        ]);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'old_password' => 'nullable|required_with:password',
            'password' => 'nullable|confirmed|min:6',
        ]);

        /* =====================
       PASSWORD UPDATE
    ===================== */
        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Old password is incorrect'
                ], 422);
            }
            $user->password = Hash::make($request->password);
        }

        /* =====================
       IMAGE UPLOAD (FIXED)
    ===================== */
        if ($request->hasFile('profile_pic')) {

            // Delete old image
            if ($user->profile_pic && Storage::disk('public')->exists($user->profile_pic)) {
                Storage::disk('public')->delete($user->profile_pic);
            }

            // Store new image
            $path = $request->file('profile_pic')->store('profiles', 'public');
            $user->profile_pic = $path;
        }

        /* =====================
       UPDATE FIELDS
    ===================== */
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully'
        ]);
    }
}
