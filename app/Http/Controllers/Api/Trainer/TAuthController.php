<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Trainer::query();

        // Optional filters
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('training_mode')) {
            $query->where('training_mode', $request->training_mode);
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        if ($request->filled('for_org_type')) {
            $query->where('for_org_type', $request->for_org_type);
        }

        $trainers = $query->paginate(10);

        return response()->json([
            'success' => true,
            'result' => $trainers,
            'msg' => 'Trainer list fetched successfully'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Trainer login
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'msg' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $login = $request->login;

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $trainer = Trainer::where($fieldType, $login)->first();

        if (!$trainer || !Hash::check($request->password, $trainer->password)) {
            return response()->json([
                'success' => false,
                'msg' => 'Invalid credentials',
            ], 401);
        }

        // Revoke old tokens
        $trainer->tokens()->delete();

        // Create new token
        $token = $trainer->createToken('TrainerToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'msg' => 'Login successful',
            'data' => [
                'token' => $token,
                'trainer' => $trainer,
            ],
        ], 200);
    }


    /**
     * Trainer signup
     */

    public function show_signup()
    {
        return view('trainer.signup');
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainers,email',
            'password' => 'required|min:6|max:32',

            'phone' => 'required|string|digits:10|unique:trainers,phone',
            'addr_line1' => 'required|string|max:255',
            'addr_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pincode' => 'required|digits:6',

            'resume_link' => 'required|url|max:500',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'biodata' => 'required|string',
            'achievements' => 'nullable|string',

            'for_org_type' => 'required|string|in:school,coorporate,both',
            'availability' => 'required|string|max:255',
            'training_mode' => 'required|string|in:online,offline,both',

            'signed_form_pdf' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'msg' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);

        // Upload profile picture
        if ($request->hasFile('profile_pic')) {
            $validated['profile_pic'] =
                $request->file('profile_pic')->store('trainer_profile_pics', 'public');
        }

        // Upload signed form PDF
        if ($request->hasFile('signed_form_pdf')) {
            $validated['signed_form_pdf'] =
                $request->file('signed_form_pdf')->store('trainer_signed_forms', 'public');
        }

        $trainer = Trainer::create($validated);
        $token = $trainer->createToken('TrainerToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'trainer' => $trainer,
                'token' => $token,
                'token_type' => 'Bearer'
            ],
            'message' => 'Trainer signup successful'
        ], 201);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainers,email',
            'password' => 'required|min:6|max:32',

            'phone' => 'nullable|string|max:15|unique:trainers,phone',
            'addr_line1' => 'required|string|max:255',
            'addr_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pincode' => 'required|digits:6',

            'resume_link' => 'nullable|url|max:500',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'biodata' => 'nullable|string',
            'achievements' => 'nullable|string',

            'for_org_type' => 'required|string|in:government,private,ngo,other',
            'availability' => 'required|string|max:255',
            'training_mode' => 'required|string|in:online,offline,hybrid',

            'signed_form_pdf' => 'nullable|file|mimes:pdf|max:5120'  // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'msg' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();
        $validated['password'] = bcrypt($validated['password']);

        // Upload profile picture
        if ($request->hasFile('profile_pic')) {
            $validated['profile_pic'] =
                $request->file('profile_pic')->store('trainer_profile_pics', 'public');
        }

        // Upload signed form PDF
        if ($request->hasFile('signed_form_pdf')) {
            $validated['signed_form_pdf'] =
                $request->file('signed_form_pdf')->store('trainer_signed_forms', 'public');
        }

        $trainer = Trainer::create($validated);

        $token = $trainer->createToken('TrainerToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Trainer signup successful',
            'data' => [
                'token' => $token,
                'trainer' => $trainer,
                'token_type' => 'Bearer'
            ],
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $trainer = Trainer::find($id);

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'msg' => 'Trainer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Trainer fetched successfully',
            'data' => $trainer
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $trainer = Trainer::find($id);
        if (!$trainer) {
            return response()->json([
                'success' => false,
                'msg' => 'Trainer not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:trainers,email,' . $trainer->trainer_id . ',trainer_id',
            'password' => 'sometimes|nullable|min:6|max:32',

            'phone' => 'sometimes|nullable|string|max:15|unique:trainers,phone,' . $trainer->trainer_id . ',trainer_id',
            'addr_line1' => 'sometimes|required|string|max:255',
            'addr_line2' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|max:255',
            'district' => 'sometimes|required|string|max:255',
            'pincode' => 'sometimes|required|digits:6',

            'resume_link' => 'sometimes|nullable|url|max:500',
            'profile_pic' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
            'biodata' => 'sometimes|nullable|string',
            'achievements' => 'sometimes|nullable|string',

            'for_org_type' => 'sometimes|required|string|in:school,coorporate,both',
            'availability' => 'sometimes|required|string|max:255',
            'training_mode' => 'sometimes|required|string|in:online,offline,both',

            'signed_form_pdf' => 'sometimes|nullable|file|mimes:pdf|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $validated = $validator->validated();

        // Update password only if provided
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Replace profile picture
        if ($request->hasFile('profile_pic')) {
            // delete old file if exists
            if ($trainer->profile_pic) {
                Storage::disk('public')->delete($trainer->profile_pic);
            }

            $validated['profile_pic'] =
                $request->file('profile_pic')->store('trainer_profile_pics', 'public');
        }

        // Replace signed form PDF
        if ($request->hasFile('signed_form_pdf')) {
            if ($trainer->signed_form_pdf) {
                Storage::disk('public')->delete($trainer->signed_form_pdf);
            }

            $validated['signed_form_pdf'] =
                $request->file('signed_form_pdf')->store('trainer_signed_forms', 'public');
        }

        $trainer->update($validated);

        return response()->json([
            'success' => true,
            'result' => $trainer,
            'message' => 'Trainer updated successfully'
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $trainer = Trainer::find($id);

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        // Delete files if exist
        if ($trainer->profile_pic) {
            Storage::disk('public')->delete($trainer->profile_pic);
        }

        if ($trainer->signed_form_pdf) {
            Storage::disk('public')->delete($trainer->signed_form_pdf);
        }

        $trainer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trainer deleted successfully'
        ], 200);
    }
    public function uploadSignedForm(Request $request)
    {
        $trainer = $request->user();

        if (!$trainer || !($trainer instanceof Trainer)) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized or invalid user type'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'signed_form_pdf' => 'required|file|mimes:pdf|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'msg' => 'Validation failed'
            ], 422);
        }

        if ($request->hasFile('signed_form_pdf')) {
            if ($trainer->signed_form_pdf && Storage::disk('public')->exists($trainer->signed_form_pdf)) {
                Storage::disk('public')->delete($trainer->signed_form_pdf);
            }
            $trainer->signed_form_pdf =
                $request->file('signed_form_pdf')->store('trainer_signed_forms', 'public');

            $trainer->save();

            return response()->json([
                'success' => true,
                'msg' => 'Signed form uploaded successfully',
                'data' => [
                    'signed_form_pdf' => $trainer->signed_form_pdf
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'msg' => 'No file uploaded'
        ], 422);
    }
}
