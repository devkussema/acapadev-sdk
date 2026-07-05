<?php

namespace Acapadev\Sdk\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Acapadev\Sdk\Facades\Acapadev;

class EnsureAcapadevSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guest()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Cache the session validation check for 5 minutes to prevent hammering the API
        $cacheKey = 'acapadev_session_valid_' . $user->getAuthIdentifier();

        $isValid = Cache::remember($cacheKey, 300, function () use ($user) {
            try {
                // Ping Acapadev ID to check if this user is still active globally
                // Assumes an endpoint /users/{id}/status exists or similar.
                $response = Acapadev::get('/users/' . $user->getAuthIdentifier() . '/status');
                
                return isset($response['active']) && $response['active'] === true;
            } catch (\Exception $e) {
                // If the central API is down, we fail open or fail closed?
                // For a robust SSO, if API is down, assume valid temporarily
                // to avoid locking out everyone during a minor outage.
                return true;
            }
        });

        if (!$isValid) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'A tua sessão foi encerrada centralmente.']);
        }

        return $next($request);
    }
}
