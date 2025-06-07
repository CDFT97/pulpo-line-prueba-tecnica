<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetApplicationLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['en', 'es']; // Idiomas soportados
        $locale = $request->query('lang', 'en'); // 'en' por defecto

        // Validar y establecer el idioma
        if (in_array($locale, $supportedLocales)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale('en'); // Idioma por defecto si no es válido
        }

        return $next($request);
    }
}
