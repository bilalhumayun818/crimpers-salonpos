<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictMobileAccess
{
    /**
     * Routes permitted on mobile devices.
     * Prefix matching is applied, so 'reset-password' also covers 'reset-password/{token}'.
     */
    private const PERMITTED_PATHS = [
        '/',
        'login',
        'logout',
        'forgot-password',
        'reset-password',
    ];

    /**
     * Handle an incoming request.
     *
     * Redirects mobile clients away from restricted routes to the Dashboard.
     * Desktop clients and requests to permitted routes are passed through unchanged.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isMobile($request) && !$this->isPermittedRoute($request)) {
            return redirect()->route('admin.index');
        }

        return $next($request);
    }

    /**
     * Determine whether the incoming request originates from a mobile device.
     *
     * Detection order:
     *  1. `Sec-CH-UA-Mobile: ?1` client hint header — explicit and takes precedence.
     *  2. User-Agent regex covering common mobile browser tokens.
     */
    private function isMobile(Request $request): bool
    {
        // Explicit client hint takes precedence over User-Agent sniffing
        if ($request->header('Sec-CH-UA-Mobile') === '?1') {
            return true;
        }

        $ua = $request->userAgent() ?? '';

        return (bool) preg_match(
            '/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile Safari/i',
            $ua
        );
    }

    /**
     * Determine whether the request path is in the set of routes permitted on mobile.
     *
     * Exact matching is used for all paths.
     * Prefix matching allows `reset-password/{token}` to pass through.
     */
    private function isPermittedRoute(Request $request): bool
    {
        $path = ltrim($request->path(), '/');

        foreach (self::PERMITTED_PATHS as $permitted) {
            $normalised = ltrim($permitted, '/');

            // Root path: empty string after ltrim matches '/'
            if ($normalised === '' && $path === '') {
                return true;
            }

            if ($normalised === '') {
                continue;
            }

            // Exact match or prefix match (e.g. reset-password/abc123)
            if ($path === $normalised || str_starts_with($path, $normalised . '/')) {
                return true;
            }
        }

        return false;
    }
}
