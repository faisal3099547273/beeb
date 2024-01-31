<?php

namespace App\Http\Middleware;

use Closure;

class MerchantMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->role === 'merchant') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
