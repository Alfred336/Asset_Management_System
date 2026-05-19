<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set timezone per authenticated user.
 */
class SetTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timezone = Auth::user()->timezone;

            if (empty($timezone)) {
                $timezone = config('app.timezone', 'UTC');
            }

            try {
                // Validate if it's a valid timezone string
                new \DateTimeZone($timezone);
                
                date_default_timezone_set($timezone);
                app('config')->set('app.timezone', $timezone);
            } catch (\Exception $e) {
                // Fallback to UTC if timezone is invalid
                date_default_timezone_set('UTC');
                app('config')->set('app.timezone', 'UTC');
            }
        }

        return $next($request);
    }
}
