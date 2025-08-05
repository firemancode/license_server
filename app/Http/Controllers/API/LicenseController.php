<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Activation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LicenseController extends Controller
{
    /**
     * Verify if a license is valid and active
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'domain' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'License key not found',
                'valid' => false
            ], 404);
        }

        // Check if license is active
        if ($license->status !== 'active') {
            return response()->json([
                'status' => 'invalid',
                'message' => 'License is not active',
                'valid' => false,
                'license_status' => $license->status
            ], 200);
        }

        // Check if license has expired
        if ($license->expires_at && Carbon::parse($license->expires_at)->isPast()) {
            // Update license status to expired
            $license->update(['status' => 'expired']);
            
            return response()->json([
                'status' => 'invalid',
                'message' => 'License has expired',
                'valid' => false,
                'expired_at' => $license->expires_at
            ], 200);
        }

        // License is valid
        $response = [
            'status' => 'valid',
            'message' => 'License is valid and active',
            'valid' => true,
            'license' => [
                'id' => $license->id,
                'license_key' => $license->license_key,
                'status' => $license->status,
                'expires_at' => $license->expires_at,
                'product' => [
                    'id' => $license->product->id,
                    'name' => $license->product->name,
                    'slug' => $license->product->slug
                ]
            ]
        ];

        // If domain is provided, check if it's activated
        if ($request->has('domain')) {
            $activation = $license->activations()
                ->where('domain', $request->domain)
                ->first();
            
            $response['activation'] = [
                'is_activated' => $activation ? true : false,
                'activated_at' => $activation ? $activation->activated_at : null
            ];
        }

        return response()->json($response, 200);
    }

    /**
     * Activate a license for a specific domain
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function activate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'status' => 'error',
                'message' => 'License key not found'
            ], 404);
        }

        // Check if license is active
        if ($license->status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'License is not active',
                'license_status' => $license->status
            ], 400);
        }

        // Check if license has expired and auto-disable
        if ($license->expires_at && Carbon::parse($license->expires_at)->isPast()) {
            $license->update(['status' => 'expired']);
            
            return response()->json([
                'status' => 'error',
                'message' => 'License has expired and has been automatically disabled',
                'expired_at' => $license->expires_at,
                'license_status' => 'expired'
            ], 400);
        }

        // Check if domain is already activated for THIS license
        $existingActivation = $license->activations()
            ->where('domain', $request->domain)
            ->first();

        if ($existingActivation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Domain is already activated for this license',
                'activation' => [
                    'domain' => $existingActivation->domain,
                    'activated_at' => $existingActivation->activated_at,
                    'ip_address' => $existingActivation->ip_address
                ]
            ], 409);
        }

        // Check if domain is already activated for ANY license (cross-license domain check)
        $domainActivatedElsewhere = Activation::where('domain', $request->domain)
            ->whereHas('license', function($query) {
                $query->where('status', 'active');
            })
            ->first();

        if ($domainActivatedElsewhere) {
            return response()->json([
                'status' => 'error',
                'message' => 'Domain is already activated for another license',
                'conflict' => [
                    'domain' => $domainActivatedElsewhere->domain,
                    'activated_at' => $domainActivatedElsewhere->activated_at,
                    'conflicting_license' => $domainActivatedElsewhere->license->license_key
                ]
            ], 409);
        }

        // Count current activations for this license
        $currentActivations = $license->activations()->count();
        $maxActivations = $license->max_activations ?? 1; // Use license-specific limit or default to 1

        if ($currentActivations >= $maxActivations) {
            // Option 1: Simply reject (default behavior)
            return response()->json([
                'status' => 'error',
                'message' => "Maximum activation limit reached ({$maxActivations} activations allowed)",
                'current_activations' => $currentActivations,
                'max_activations' => $maxActivations,
                'existing_activations' => $license->activations()->select('domain', 'activated_at', 'ip_address')->get()
            ], 429);

            // Option 2: Auto-disable license (uncomment to enable)
            /*
            $license->update(['status' => 'disabled']);
            return response()->json([
                'status' => 'error',
                'message' => "Maximum activation limit exceeded. License has been automatically disabled.",
                'current_activations' => $currentActivations,
                'max_activations' => $maxActivations,
                'license_status' => 'disabled'
            ], 429);
            */
        }

        // Create new activation
        $activation = Activation::create([
            'license_id' => $license->id,
            'domain' => $request->domain,
            'ip_address' => $request->ip(),
            'activated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'License activated successfully for domain',
            'activation' => [
                'id' => $activation->id,
                'domain' => $activation->domain,
                'ip_address' => $activation->ip_address,
                'activated_at' => $activation->activated_at
            ],
            'license' => [
                'license_key' => $license->license_key,
                'status' => $license->status,
                'product_name' => $license->product->name,
                'total_activations' => $license->activations()->count(),
                'max_activations' => $maxActivations
            ]
        ], 201);
    }

    /**
     * Deactivate a license for a specific domain
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deactivate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string',
            'domain' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'status' => 'error',
                'message' => 'License key not found'
            ], 404);
        }

        // Find activation for this domain
        $activation = $license->activations()
            ->where('domain', $request->domain)
            ->first();

        if (!$activation) {
            return response()->json([
                'status' => 'error',
                'message' => 'No activation found for this domain and license'
            ], 404);
        }

        // Delete the activation
        $activationData = [
            'domain' => $activation->domain,
            'activated_at' => $activation->activated_at,
            'ip_address' => $activation->ip_address
        ];

        $activation->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'License deactivated successfully for domain',
            'deactivated_activation' => $activationData,
            'license' => [
                'license_key' => $license->license_key,
                'status' => $license->status,
                'product_name' => $license->product->name
            ]
        ], 200);
    }
}