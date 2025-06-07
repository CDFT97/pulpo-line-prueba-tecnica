<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse; // Importar JsonResponse

class CustomSanctumAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Intentamos autenticar con el guard 'sanctum' y obtener el usuario.
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            // Si la autenticación falla (no hay token válido, o no se encontró usuario)
            return new JsonResponse([
                'message' => 'Acceso no autorizado. Por favor, proporcione un token de autenticación válido.',
                'status' => 'error',
                'timestamp' => now()->toDateTimeString(),
            ], 401);
        }

        
        // 1. Establece el usuario autenticado en la solicitud.
        $request->setUserResolver(fn() => $user);

        // 2. Establece el guard 'sanctum' como el guard por defecto para esta solicitud.
        Auth::shouldUse('sanctum');

        // Si la autenticación es exitosa, continuamos con la solicitud.
        return $next($request);
    }
}
