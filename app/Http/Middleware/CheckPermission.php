<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Verificar si el usuario tiene el permiso requerido
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json([
                'message' => __('messages.errors.unauthorized'),
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }
}
