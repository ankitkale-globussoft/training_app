<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully');
    }

    public function viewforgotPassword()
    {
        return view('admin.forgot-pass');
    }

    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->where('user_type', 'admin');
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        $resetLink = url('/admin/reset-password/' . $token . '?email=' . $request->email);

        Mail::send('emails.reset-password', ['link' => $resetLink], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Your Password');
        });

        return redirect()->route('trainer.login')->with('success', 'Password reset link sent to your email.');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('admin.reset-pass', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->where('user_type', 'admin');
                }),
            ],
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
                'msg'     => 'Validation failed'
            ], 422);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json([
                'errors' => [
                    'token' => ['Token expired, retry forgot password']
                ]
            ], 422);
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect('/admin/login')->with('success', 'Password reset successfully.');
    }
}
