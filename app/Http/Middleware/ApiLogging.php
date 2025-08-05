<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLogging
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log API requests (you can adjust this condition as needed)
        if ($request->is('api/*')) {
            $this->logApiRequest($request, $response);
        }

        return $response;
    }

    /**
     * Log the API request to the database.
     */
    private function logApiRequest(Request $request, Response $response): void
    {
        try {
            // Extract license key from request (could be in header, query param, or body)
            $licenseKey = $this->extractLicenseKey($request);
            
            // Extract domain from referer or a custom header
            $domain = $this->extractDomain($request);

            ApiLog::create([
                'endpoint' => $request->getPathInfo(),
                'ip_address' => $request->ip(),
                'license_key' => $licenseKey,
                'domain' => $domain,
                'user_agent' => $request->userAgent(),
                'status_code' => $response->getStatusCode(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the API response
            // You might want to log this error somewhere else
        }
    }

    /**
     * Extract license key from various possible locations in the request.
     */
    private function extractLicenseKey(Request $request): ?string
    {
        // Check Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check custom license header
        if ($request->hasHeader('X-License-Key')) {
            return $request->header('X-License-Key');
        }

        // Check query parameter
        if ($request->has('license_key')) {
            return $request->query('license_key');
        }

        // Check request body
        if ($request->has('license_key')) {
            return $request->input('license_key');
        }

        return null;
    }

    /**
     * Extract domain from request.
     */
    private function extractDomain(Request $request): ?string
    {
        // Check custom domain header
        if ($request->hasHeader('X-Domain')) {
            return $request->header('X-Domain');
        }

        // Extract from referer
        $referer = $request->header('Referer');
        if ($referer) {
            $parsed = parse_url($referer);
            return $parsed['host'] ?? null;
        }

        // Check request body for domain
        if ($request->has('domain')) {
            return $request->input('domain');
        }

        return null;
    }
}