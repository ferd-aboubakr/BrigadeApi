<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->is('api/*')) {
            return null; // Don't redirect API requests
        }
        
        return $request->expectsJson() ? null : route('login');
    }
    
    protected function unauthenticated($request, array $guards)
    {
        if ($request->is('api/*')) {
            abort(response()->json(['message' => 'Unauthenticated'], 401));
        }
        
        parent::unauthenticated($request, $guards);
    }
}
