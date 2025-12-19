<?php

namespace App\Http\Controllers\Api\Org;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $login = $request->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $org = Organization::where($fieldType, $login)->first();

        if (!$org || !Hash::check($request->password, $org->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'errors'  => [
                    'login' => ['Invalid credentials.'],
                ],
            ], 422);
        }

        $token = $org->createToken('org-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'organization' => $org,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
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
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);

        if ($request->hasFile('org_image')) {
            $validated['org_image'] =
                $request->file('org_image')->store('org_profile_pics', 'public');
        }

        $org = Organization::create($validated);

        $token = $org->createToken('org-api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Organisation registered successfully',
            'data' => [
                'organization' => $org,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    public function update(Request $request)
    {
        $org = Organization::findOrFail(Auth::guard('org_web')->user()->org_id);
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'rep_designation' => 'required|string|max:255',

            'email'           => 'required|email|unique:organizations,email,' . $org->org_id . ',org_id',
            'mobile'          => 'required|digits:10|unique:organizations,mobile,' . $org->org_id . ',org_id',
            'alt_mobile'      => 'nullable|digits:10',

            'org_image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'addr_line1'      => 'required|string|max:255',
            'addr_line2'      => 'nullable|string|max:255',
            'city'            => 'required|string|max:255',
            'district'        => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'pincode'         => 'required|digits:6',

            'old_password'    => 'nullable|required_with:password',
            'password'        => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        /* Password Update (unchanged logic) */
        if (!empty($data['password'])) {
            if (!Hash::check($data['old_password'], $org->password)) {
                return response()->json([
                    'errors' => [
                        'old_password' => ['Old password is incorrect'],
                    ],
                ], 422);
            }
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password'], $data['old_password']);
        }

        /* Image Upload (unchanged) */
        if ($request->hasFile('org_image')) {
            $data['org_image'] = $request->file('org_image')
                ->store('org_profile_pics', 'public');
        }

        $org->update($data);

        return response()->json([
            'success' => true,
            'msg' => 'Profile updated successfully',
        ]);
    }
}