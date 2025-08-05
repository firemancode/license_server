<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Product;
use App\Models\User;
use App\Http\Requests\Admin\LicenseRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class LicenseController extends Controller
{
    /**
     * Display a listing of licenses.
     */
    public function index(Request $request): View
    {
        $query = License::with(['user', 'product', 'activations']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by product
        if ($request->has('product') && $request->product !== '') {
            $query->where('product_id', $request->product);
        }

        // Search by license key or user
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_key', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Check for expired licenses
        if ($request->has('expired') && $request->expired === '1') {
            $query->where('expires_at', '<', now())
                  ->where('status', '!=', 'expired');
        }

        $licenses = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get data for filters
        $products = Product::orderBy('name')->get();
        $statuses = ['active', 'expired', 'disabled', 'suspended'];

        return view('admin.licenses.index', compact('licenses', 'products', 'statuses'));
    }

    /**
     * Show the form for creating a new license.
     */
    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('admin.licenses.create', compact('products', 'users'));
    }

    /**
     * Store a newly created license in storage.
     */
    public function store(LicenseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Auto-generate license key if not provided
        if (empty($data['license_key'])) {
            do {
                $data['license_key'] = $this->generateLicenseKey();
            } while (License::where('license_key', $data['license_key'])->exists());
        }

        $license = License::create($data);

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', "License '{$license->license_key}' created successfully!");
    }

    /**
     * Display the specified license.
     */
    public function show(License $license): View
    {
        $license->load(['user', 'product', 'activations']);

        return view('admin.licenses.show', compact('license'));
    }

    /**
     * Show the form for editing the specified license.
     */
    public function edit(License $license): View
    {
        $products = Product::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('admin.licenses.edit', compact('license', 'products', 'users'));
    }

    /**
     * Update the specified license in storage.
     */
    public function update(LicenseRequest $request, License $license): RedirectResponse
    {
        $data = $request->validated();
        
        $license->update($data);

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', "License '{$license->license_key}' updated successfully!");
    }

    /**
     * Remove the specified license from storage.
     */
    public function destroy(License $license): RedirectResponse
    {
        $licenseKey = $license->license_key;
        
        // Delete associated activations first
        $license->activations()->delete();
        
        $license->delete();

        return redirect()
            ->route('admin.licenses.index')
            ->with('success', "License '{$licenseKey}' and all its activations deleted successfully!");
    }

    /**
     * Block a license (set status to disabled).
     */
    public function block(License $license): RedirectResponse
    {
        $license->update(['status' => 'disabled']);

        return redirect()
            ->back()
            ->with('success', "License '{$license->license_key}' has been blocked successfully!");
    }

    /**
     * Activate a license (set status to active).
     */
    public function activate(License $license): RedirectResponse
    {
        $license->update(['status' => 'active']);

        return redirect()
            ->back()
            ->with('success', "License '{$license->license_key}' has been activated successfully!");
    }

    /**
     * Reset all activations for a license.
     */
    public function resetActivations(License $license): RedirectResponse
    {
        $activationsCount = $license->activations()->count();
        $license->activations()->delete();

        return redirect()
            ->back()
            ->with('success', "Reset {$activationsCount} activation(s) for license '{$license->license_key}'!");
    }

    /**
     * Bulk actions for licenses.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:activate,block,delete,reset_activations',
            'licenses' => 'required|array|min:1',
            'licenses.*' => 'exists:licenses,id'
        ]);

        $action = $request->action;
        $licenseIds = $request->licenses;
        $licenses = License::whereIn('id', $licenseIds)->get();

        switch ($action) {
            case 'activate':
                License::whereIn('id', $licenseIds)->update(['status' => 'active']);
                return redirect()
                    ->route('admin.licenses.index')
                    ->with('success', "Successfully activated " . count($licenseIds) . " license(s).");

            case 'block':
                License::whereIn('id', $licenseIds)->update(['status' => 'disabled']);
                return redirect()
                    ->route('admin.licenses.index')
                    ->with('success', "Successfully blocked " . count($licenseIds) . " license(s).");

            case 'delete':
                // Delete activations first
                foreach ($licenses as $license) {
                    $license->activations()->delete();
                }
                License::whereIn('id', $licenseIds)->delete();
                return redirect()
                    ->route('admin.licenses.index')
                    ->with('success', "Successfully deleted " . count($licenseIds) . " license(s) and their activations.");

            case 'reset_activations':
                $totalActivations = 0;
                foreach ($licenses as $license) {
                    $totalActivations += $license->activations()->count();
                    $license->activations()->delete();
                }
                return redirect()
                    ->route('admin.licenses.index')
                    ->with('success', "Reset activations for " . count($licenseIds) . " license(s). Total {$totalActivations} activation(s) removed.");

            default:
                return redirect()
                    ->route('admin.licenses.index')
                    ->with('error', 'Invalid action.');
        }
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
     * Generate a random license key.
     */
    private function generateLicenseKey(): string
    {
        return strtoupper(
            Str::random(5) . '-' . 
            Str::random(5) . '-' . 
            Str::random(5)
        );
    }
}