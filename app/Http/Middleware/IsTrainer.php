<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsTrainer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get authenticated user from any guard (sanctum handles both users and trainers)
        $user = $request->user();

        // Check if user exists and is a Trainer model instance
        if (!$user || !($user instanceof \App\Models\Trainer)) {
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized. Trainer access only.'
            ], 403);
        }

        // Check if trainer is verified
        if ($user->verified === 'pending') {
            if ($request->route()->getName() !== 'api.trainer.upload-signed-form') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Your account is waiting for verification. Please wait until an admin approves your profile.'
                ], 403);
            }
        }

        // Check if trainer is suspended
        if ($user->verified === 'suspended') {
            if ($request->route()->getName() !== 'api.trainer.upload-signed-form') {
                return response()->json([
                    'success' => false,
                    'msg' => 'Your account has been suspended. Please contact administration for more details.'
                ], 403);
            }
        }

        return $next($request);
    }

}
