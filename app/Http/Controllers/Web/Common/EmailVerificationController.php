<?php

namespace App\Http\Controllers\Web\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\EmailVerificationOtpMail;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:trainer,org'
        ]);

        $email = $request->email;
        $type = $request->type;

        // Check if email already exists
        if ($type === 'trainer') {
            if (\App\Models\Trainer::where('email', $email)->exists()) {
                throw ValidationException::withMessages(['email' => 'This email is already registered.']);
            }
        } elseif ($type === 'org') {
            if (\App\Models\Organization::where('email', $email)->exists()) {
                throw ValidationException::withMessages(['email' => 'This email is already registered.']);
            }
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store in Cache for 10 minutes
        Cache::put('email_verification_' . $email, $otp, 600);

        // Send Email
        Mail::to($email)->send(new EmailVerificationOtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to your email.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $cachedOtp = Cache::get('email_verification_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired verification code.']);
        }

        // Verification successful. 
        // We can return a token or signed status, but for this simple in-page implementation,
        // the frontend just needs a success confirmation to unlock the submit button.
        // For extra security, we could return a signed token to submit with the form, 
        // but given the request scope, checking cache on backend Registration is robust enough? 
        // Actually, re-verifying on registration submit is best practice. 
        // So we will keep the OTP in cache or mark it as verified in cache.

        // Let's mark as verified in cache for a longer duration to allow form submission time
        Cache::put('email_verified_' . $request->email, true, 3600); // Valid for 1 hour

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.'
        ]);
    }
}
