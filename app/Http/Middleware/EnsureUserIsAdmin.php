<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // Substitua pelo seu email ou uma verificação de 'is_admin' no banco
    if ($request->user() && $request->user()->email === 'ednonunes@gmail.com') {
        return $next($request);
    }

    abort(403, 'Acesso não autorizado.');
}
}
