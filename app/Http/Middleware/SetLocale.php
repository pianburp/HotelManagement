<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has a locale set in session
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            
            // Validate if it's in our available locales
            $availableLocales = config('app.available_locales', ['en']);
            if (in_array($locale, $availableLocales)) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }
}
