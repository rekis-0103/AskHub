<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->fresh()?->isSuspended()) {
            return back()->with('error', 'Your account is suspended from posting or voting.');
        }

        return $next($request);
    }
}
