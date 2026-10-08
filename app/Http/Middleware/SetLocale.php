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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if locale is provided in the request
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if (in_array($locale, ['ar', 'en'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        } else {
            // Use session locale or default to Arabic
            $locale = Session::get('locale', 'ar');
            App::setLocale($locale);
        }

        return $next($request);
    }
}
