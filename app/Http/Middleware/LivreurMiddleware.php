<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LivreurMiddleware
{
    
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isLivreur()) {
            return redirect()->route('login')->with('error', 'Accès réservé aux livreurs.');
        }

        return $next($request);
    }
}
