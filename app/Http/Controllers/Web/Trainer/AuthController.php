<?php

namespace App\Http\Controllers\Web\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function show_login()
    {
        return view('trainer.login');
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

        // Find trainer by email or phone
        $trainer = Trainer::where($fieldType, $login)->first();

        if (!$trainer || !Hash::check($request->password, $trainer->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'login' => ['Invalid credentials.']
                ],
                'msg' => 'Login failed'
            ], 422);
        }

        $remember = (bool) $request->remember;
        Auth::guard('trainer_web')->login($trainer, $remember);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'msg' => 'Login successful!',
            'redirect' => route('trainer.dashboard')
        ]);
    }

    public function show_register()
    {
        return view('trainer.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:trainers,email',
            'password'      => 'required|min:6|max:32',

            'phone'         => 'required|string|digits:10|unique:trainers,phone',
            'addr_line1'    => 'required|string|max:255',
            'addr_line2'    => 'nullable|string|max:255',
            'city'          => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'pincode'       => 'required|digits:6',

            'resume_link'   => 'required|url|max:500',
            'profile_pic'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'biodata'       => 'required|string',
            'achievements'  => 'nullable|string',

            'for_org_type'  => 'required|string|in:school,corporate,both',
            'availability'  => 'required|string|max:255',
            'training_mode' => 'required|string|in:online,offline,both',

            'signed_form_pdf' => 'required|file|mimes:pdf|max:5120'
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
            $validated['profile_pic'] =
                $request->file('profile_pic')->store('trainer_profile_pics', 'public');
        }

        if ($request->hasFile('signed_form_pdf')) {
            $validated['signed_form_pdf'] =
                $request->file('signed_form_pdf')->store('trainer_signed_forms', 'public');
        }

        $trainer = Trainer::create($validated);

        Auth::login($trainer);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'msg'     => 'Trainer signup successful',
            'redirect' => route('trainer.dashboard')
        ], 201);
    }

    public function update(Request $request)
    {
        // IMPORTANT: fetch real model
        $trainer = Trainer::findOrFail(Auth::guard('trainer_web')->user()->trainer_id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainers,email,' . $trainer->id,
            'phone' => 'required|digits:10|unique:trainers,phone,' . $trainer->id,

            'addr_line1' => 'required|string|max:255',
            'addr_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|digits:6',

            'resume_link' => 'required|url|max:500',
            'biodata' => 'required|string',
            'achievements' => 'nullable|string',

            'for_org_type' => 'required|in:school,corporate,both',
            'training_mode' => 'required|in:online,offline,both',
            'availability' => 'required|string|max:255',

            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'signed_form_pdf' => 'nullable|mimes:pdf|max:5120',

            'password' => 'nullable|confirmed|min:6',
            'old_password' => 'required_with:password',
        ]);

        /* =====================
       PASSWORD
    ===================== */
        if ($request->filled('password')) {
            if (!Hash::check($request->old_password, $trainer->password)) {
                return response()->json([
                    'errors' => [
                        'old_password' => ['Old password is incorrect']
                    ]
                ], 422);
            }
            $trainer->password = bcrypt($request->password);
        }

        /* =====================
       FILE UPLOADS
    ===================== */
        if ($request->hasFile('profile_pic')) {
            if ($trainer->profile_pic && Storage::disk('public')->exists($trainer->profile_pic)) {
                Storage::disk('public')->delete($trainer->profile_pic);
            }
            $trainer->profile_pic =
                $request->file('profile_pic')->store('trainer_profile_pics', 'public');
        }

        if ($request->hasFile('signed_form_pdf')) {
            if ($trainer->signed_form_pdf && Storage::disk('public')->exists($trainer->signed_form_pdf)) {
                Storage::disk('public')->delete($trainer->signed_form_pdf);
            }
            $trainer->signed_form_pdf =
                $request->file('signed_form_pdf')->store('trainer_signed_forms', 'public');
        }

        /* =====================
       MANUAL FIELD ASSIGN
    ===================== */
        $trainer->name          = $request->name;
        $trainer->email         = $request->email;
        $trainer->phone         = $request->phone;
        $trainer->addr_line1    = $request->addr_line1;
        $trainer->addr_line2    = $request->addr_line2;
        $trainer->city          = $request->city;
        $trainer->district      = $request->district;
        $trainer->state         = $request->state;
        $trainer->pincode       = $request->pincode;
        $trainer->resume_link   = $request->resume_link;
        $trainer->biodata       = $request->biodata;
        $trainer->achievements  = $request->achievements;
        $trainer->for_org_type  = $request->for_org_type;
        $trainer->training_mode = $request->training_mode;
        $trainer->availability  = $request->availability;

        // ✅ REAL ELOQUENT SAVE
        $trainer->save();

        return response()->json([
            'message' => 'Trainer profile updated successfully'
        ]);
    }


    public function logout(Request $request)
    {
        Auth::guard('trainer_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('trainer.login')->with('success', 'Logged out successfully');
    }
}
