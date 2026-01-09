<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsStudentApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $student = Auth::guard('student_api')->user();
        if(!$student){
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized. Plese login to proceed.'
            ], 403);
        }
        return $next($request);
    }
}
