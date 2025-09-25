<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = auth()->user();

        // Check if user can access dashboard
        if (! $user->canAccessDashboard()) {
            // Redirect regular users away from dashboard
            return redirect()->route('home')->with('error', 'Access denied. You do not have permission to access the dashboard.');
        }

        return $next($request);
    }
}
