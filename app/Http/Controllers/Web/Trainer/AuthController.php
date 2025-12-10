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

    public function logout(Request $request){
        Auth::guard('trainer_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('trainer.login')->with('success', 'Logged out successfully');
    }
}
