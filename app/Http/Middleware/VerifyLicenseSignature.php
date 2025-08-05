<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class VerifyLicenseSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get signature and timestamp from headers
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        
        // Check if required headers are present
        if (!$signature || !$timestamp) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing required headers: X-Signature and X-Timestamp'
            ], 401);
        }

        // Validate timestamp format and check if it's not too old (5 minutes)
        try {
            $requestTime = Carbon::createFromTimestamp($timestamp);
            $now = Carbon::now();
            
            if ($now->diffInMinutes($requestTime) > 5) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Request timestamp is too old (must be within 5 minutes)'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid timestamp format'
            ], 401);
        }

        // Get request data
        $licenseKey = $request->input('license_key');
        $domain = $request->input('domain', '');
        
        if (!$licenseKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'license_key is required for signature verification'
            ], 401);
        }

        // Create the data string for HMAC verification
        $dataString = $licenseKey . $domain . $timestamp;
        
        // Get secret key from environment
        $secretKey = config('app.license_secret');
        
        if (!$secretKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'License secret key not configured'
            ], 500);
        }

        // Generate expected signature
        $expectedSignature = hash_hmac('sha256', $dataString, $secretKey);
        
        // Verify signature using timing-safe comparison
        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature'
            ], 401);
        }

        // Signature is valid, continue with the request
        return $next($request);
    }
}
