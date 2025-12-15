<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->user_type !== 'admin') {
            //for api
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'msg'     => 'Unauthorized. Admin access only.'
                ], 403);
            }
            // for web 
            return redirect()->route('admin.login')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}
