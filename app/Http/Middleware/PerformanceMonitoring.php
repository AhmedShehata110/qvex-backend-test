<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoring
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $response = $next($request);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        $memoryUsage = ($endMemory - $startMemory) / 1024 / 1024; // Convert to MB

        // Log slow requests
        if ($executionTime > config('performance.monitoring.slow_query_threshold', 1000)) {
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime.'ms',
                'memory_usage' => $memoryUsage.'MB',
                'user_id' => auth()->id(),
            ]);
        }

        // Log high memory usage
        if ($memoryUsage > config('performance.monitoring.memory_limit_warning', 128)) {
            Log::warning('High memory usage detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => $executionTime.'ms',
                'memory_usage' => $memoryUsage.'MB',
                'user_id' => auth()->id(),
            ]);
        }

        // Add performance headers in debug mode
        if (config('app.debug')) {
            $response->headers->set('X-Execution-Time', $executionTime.'ms');
            $response->headers->set('X-Memory-Usage', $memoryUsage.'MB');
        }

        return $response;
    }
}
