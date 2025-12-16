<?php

namespace App\Http\Controllers\Web\Org;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('organisation.login');
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
        $org = Organization::where($fieldType, $login)->first();

        if (!$org || !Hash::check($request->password, $org->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'login' => ['Invalid credentials.']
                ],
                'msg' => 'Login failed'
            ], 422);
        }

        
        $remember = (bool) $request->remember;
        Auth::guard('org_web')->login($org, $remember);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'msg' => 'Login successful!',
            'redirect' => route('org.home')
        ]);
    }

    public function showRegisterForm()
    {
        return view('organisation.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'rep_designation' => 'required|string|max:255',

            'email'           => 'required|email|unique:organizations,email',
            'mobile'          => 'required|digits:10|unique:organizations,mobile',
            'alt_mobile'      => 'nullable|digits:10',
            'org_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'addr_line1'      => 'required|string|max:255',
            'addr_line2'      => 'nullable|string|max:255',
            'city'            => 'required|string|max:255',
            'district'        => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'pincode'         => 'required|digits:6',

            'password'        => 'required|min:6|max:32',
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

        if ($request->hasFile('org_image')) {
            $validated['org_image'] = $request->file('org_image')->store('org_profile_pics', 'public');
        }

        $org = Organization::create($validated);
        Auth::login($org);
        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'msg'      => 'Organisation registered successfully',
            'redirect' => route('org.home')
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::guard('org_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('org.login')->with('success', 'Logged out successfully');
    }
}
