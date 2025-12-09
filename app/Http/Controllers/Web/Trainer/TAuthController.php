<?php

namespace App\Http\Controllers\Web\Trainer;

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
    public function index()
    {
        //
    }

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
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                    'msg'     => 'Validation failed'
                ], 422);
            }
            // web
            return back()->withErrors($validator)->withInput();
        }

        $login = $request->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Find trainer by email or phone
        $trainer = Trainer::where($fieldType, $login)->first();

        if (!$trainer || !Hash::check($request->password, $trainer->password)) {
            return back()->withErrors([
                'login' => 'Invalid credentials'
            ]);
        }

        Auth::login($trainer);
        $request->session()->regenerate();
        return redirect()->route('dashboard')->with('success', 'Login successful');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
