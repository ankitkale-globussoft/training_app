<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsTrainerWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $trainer = Auth::guard('trainer_web')->user();
        
        if (!$trainer) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Unauthorized. Trainer access only.'
                ], 403);
            }
            
            return redirect()->route('trainer.login')->with('error', 'Please login to continue.');
        }
        
        return $next($request);
    }

}
