<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTrailingSlash
{
    /**
     * Permanently redirect GET/HEAD requests with a trailing slash to the canonical slash-less URL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();

        if ($path !== '/' && str_ends_with($path, '/') && $request->isMethodSafe()) {
            $query = $request->getQueryString();
            $target = $request->getSchemeAndHttpHost().$request->getBaseUrl().rtrim($path, '/').($query !== null ? '?'.$query : '');

            return redirect($target, 301);
        }

        return $next($request);
    }
}
