<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckToken
{
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = 'SkFabTZibXE1aE14ckpQUUxHc2dnQ2RzdlFRTTM2NFE2cGI4d3RQNjZmdEFITmdBQkE=';

        if ($request->bearerToken() !== $bearerToken) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
