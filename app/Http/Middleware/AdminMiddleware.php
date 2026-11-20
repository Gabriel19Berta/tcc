<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->admin) {
            abort(403, 'Você não possui permissão para acessar esta área.');
        }

        return $next($request);
    }
}
