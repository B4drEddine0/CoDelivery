<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isClient()) {
            return redirect()->route('login')->with('error', 'Accès réservé aux clients.');
        }

        return $next($request);
    }
}
