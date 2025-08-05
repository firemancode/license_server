<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Activation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with license overview.
     */
    public function dashboard(): View
    {
        $totalLicenses = License::count();
        $activeLicenses = License::where('status', 'active')->count();
        $expiredLicenses = License::where('status', 'expired')->count();
        $totalActivations = Activation::count();

        // Recent activations
        $recentActivations = Activation::with(['license.product'])
            ->orderBy('activated_at', 'desc')
            ->limit(10)
            ->get();

        // Licenses with activations
        $licensesWithActivations = License::with(['activations', 'product'])
            ->has('activations')
            ->get();

        return view('admin.dashboard', compact(
            'totalLicenses',
            'activeLicenses', 
            'expiredLicenses',
            'totalActivations',
            'recentActivations',
            'licensesWithActivations'
        ));
    }

    /**
     * Display all licenses with their activations.
     */
    public function licenses(): View
    {
        $licenses = License::with(['activations', 'product', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.licenses', compact('licenses'));
    }

    /**
     * Reset all activations for a specific license.
     */
    public function resetActivations(Request $request, License $license): RedirectResponse
    {
        $activationsCount = $license->activations()->count();
        
        // Delete all activations for this license
        $license->activations()->delete();

        return redirect()->back()->with('success', 
            "Successfully reset {$activationsCount} activation(s) for license {$license->license_key}"
        );
    }

    /**
     * Reset activations via API (for admin tools).
     */
    public function resetActivationsApi(Request $request): JsonResponse
    {
        $request->validate([
            'license_key' => 'required|string|exists:licenses,license_key'
        ]);

        $license = License::where('license_key', $request->license_key)->first();
        
        if (!$license) {
            return response()->json([
                'status' => 'error',
                'message' => 'License not found'
            ], 404);
        }

        $activationsCount = $license->activations()->count();
        
        // Store activation data before deletion for logging
        $deletedActivations = $license->activations()
            ->select('domain', 'ip_address', 'activated_at')
            ->get()
            ->toArray();

        // Delete all activations
        $license->activations()->delete();

        return response()->json([
            'status' => 'success',
            'message' => "Successfully reset {$activationsCount} activation(s) for license {$license->license_key}",
            'license_key' => $license->license_key,
            'deleted_activations_count' => $activationsCount,
            'deleted_activations' => $deletedActivations,
            'reset_at' => now()->toISOString()
        ]);
    }

    /**
     * Manually activate/deactivate license status.
     */
    public function updateLicenseStatus(Request $request, License $license): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:active,expired,disabled,suspended'
        ]);

        $oldStatus = $license->status;
        $license->update(['status' => $request->status]);

        return redirect()->back()->with('success', 
            "License {$license->license_key} status changed from '{$oldStatus}' to '{$request->status}'"
        );
    }

    /**
     * Delete a specific activation.
     */
    public function deleteActivation(Activation $activation): RedirectResponse
    {
        $licenseKey = $activation->license->license_key;
        $domain = $activation->domain;
        
        $activation->delete();

        return redirect()->back()->with('success', 
            "Activation for domain '{$domain}' removed from license {$licenseKey}"
        );
    }

    /**
     * Bulk operations on licenses.
     */
    public function bulkOperation(Request $request): RedirectResponse
    {
        $request->validate([
            'operation' => 'required|in:reset_all_activations,disable_expired',
            'license_ids' => 'sometimes|array',
            'license_ids.*' => 'exists:licenses,id'
        ]);

        $operation = $request->operation;
        $licenseIds = $request->license_ids ?? [];

        switch ($operation) {
            case 'reset_all_activations':
                if (empty($licenseIds)) {
                    return redirect()->back()->with('error', 'No licenses selected');
                }
                
                $totalDeleted = 0;
                foreach ($licenseIds as $licenseId) {
                    $license = License::find($licenseId);
                    $totalDeleted += $license->activations()->count();
                    $license->activations()->delete();
                }
                
                return redirect()->back()->with('success', 
                    "Reset activations for " . count($licenseIds) . " license(s), deleted {$totalDeleted} activation(s)"
                );

            case 'disable_expired':
                $expiredCount = License::where('status', 'active')
                    ->where('expires_at', '<', now())
                    ->update(['status' => 'expired']);
                
                return redirect()->back()->with('success', 
                    "Disabled {$expiredCount} expired license(s)"
                );

            default:
                return redirect()->back()->with('error', 'Invalid operation');
        }
    }
}