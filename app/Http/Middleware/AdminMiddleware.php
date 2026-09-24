<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (int)$request->user()->role_id !== 1) {
            return redirect()->route('tienda.inicio')->with('error', 'Acceso restringido. Esta sección es exclusiva para administradores.');
        }

        return $next($request);
    }
}
