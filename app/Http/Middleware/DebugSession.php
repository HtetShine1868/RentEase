<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugSession
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.debug')) {
            Log::info('Session Debug', [
                'url' => $request->url(),
                'session_id' => session()->getId(),
                'csrf_token' => csrf_token(),
                'previous_token' => $request->session()->token(),
                'cookies' => $request->cookies->all(),
            ]);
        }
        
        return $next($request);
    }
}