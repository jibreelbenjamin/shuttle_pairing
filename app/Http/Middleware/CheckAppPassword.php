<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('app_authenticated')) {
            return redirect()->route('password.form');
        }

        return $next($request);
    }
}
