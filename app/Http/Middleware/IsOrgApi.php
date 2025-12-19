<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsOrgApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $org = Auth::guard('org_api')->user();
        
        if(!$org){
            return response()->json([
                'success' => false,
                'msg' => 'Unauthorized. Trainer access only.'
            ], 403);
        }

        return $next($request);
    }
}
