<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        if (session()->has('branch_id')) {
            return $next($request);
        }

        if ($request->routeIs('branch.selection')) {
            return $next($request);
        }

        return redirect()->route('branch.selection');
    }
}