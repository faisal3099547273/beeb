<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // dd($);
        // dd(auth());
       if ($request->user() && $request->user()->role === 'merchant') {
            return $next($request);
        }

        return response()->json([
            'code' => 401,
            'status' => 'unauthorized',
            'message' => 'Merchant not authenticated.',
        ], 401);
    }
}
