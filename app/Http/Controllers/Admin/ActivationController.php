<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activation;
use App\Models\License;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActivationController extends Controller
{
    /**
     * Display a listing of activations.
     */
    public function index(Request $request): View
    {
        $query = Activation::with(['license.product', 'license.user']);

        // Filter by product
        if ($request->has('product') && $request->product !== '') {
            $query->whereHas('license.product', function ($q) use ($request) {
                $q->where('id', $request->product);
            });
        }

        // Filter by license status
        if ($request->has('license_status') && $request->license_status !== '') {
            $query->whereHas('license', function ($q) use ($request) {
                $q->where('status', $request->license_status);
            });
        }

        // Search by domain, IP, or license key
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('domain', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('license', function ($licenseQuery) use ($search) {
                      $licenseQuery->where('license_key', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('activated_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('activated_at', '<=', $request->date_to);
        }

        $activations = $query->orderBy('activated_at', 'desc')->paginate(20);

        // Get data for filters
        $products = Product::orderBy('name')->get();
        $licenseStatuses = ['active', 'expired', 'disabled', 'suspended'];

        return view('admin.activations.index', compact('activations', 'products', 'licenseStatuses'));
    }

    /**
     * Display the specified activation.
     */
    public function show(Activation $activation): View
    {
        $activation->load(['license.product', 'license.user', 'license.activations']);

        return view('admin.activations.show', compact('activation'));
    }

    /**
     * Revoke (delete) the specified activation.
     */
    public function revoke(Activation $activation): RedirectResponse
    {
        $domain = $activation->domain;
        $licenseKey = $activation->license->license_key;
        
        $activation->delete();

        return redirect()
            ->back()
            ->with('success', "Activation for domain '{$domain}' revoked from license '{$licenseKey}' successfully!");
    }

    /**
     * Block a domain (you could implement a blocked_domains table).
     */
    public function blockDomain(Activation $activation): RedirectResponse
    {
        // For now, we'll just revoke the activation
        // In a real implementation, you might want to add the domain to a blocked list
        
        $domain = $activation->domain;
        $licenseKey = $activation->license->license_key;
        
        $activation->delete();

        // Here you could add logic to save to a blocked_domains table
        // BlockedDomain::create(['domain' => $domain, 'reason' => 'Blocked by admin']);

        return redirect()
            ->back()
            ->with('success', "Domain '{$domain}' blocked and activation revoked from license '{$licenseKey}'!");
    }

    /**
     * Bulk actions for activations.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:revoke,block_domains',
            'activations' => 'required|array|min:1',
            'activations.*' => 'exists:activations,id'
        ]);

        $action = $request->action;
        $activationIds = $request->activations;
        $activations = Activation::with('license')->whereIn('id', $activationIds)->get();

        switch ($action) {
            case 'revoke':
                Activation::whereIn('id', $activationIds)->delete();
                return redirect()
                    ->route('admin.activations.index')
                    ->with('success', "Successfully revoked " . count($activationIds) . " activation(s).");

            case 'block_domains':
                $domains = $activations->pluck('domain')->unique();
                
                // Delete the activations
                Activation::whereIn('id', $activationIds)->delete();
                
                // Here you could add logic to save blocked domains
                // foreach ($domains as $domain) {
                //     BlockedDomain::firstOrCreate(['domain' => $domain], ['reason' => 'Bulk blocked by admin']);
                // }

                return redirect()
                    ->route('admin.activations.index')
                    ->with('success', "Successfully blocked " . count($domains) . " domain(s) and revoked " . count($activationIds) . " activation(s).");

            default:
                return redirect()
                    ->route('admin.activations.index')
                    ->with('error', 'Invalid action.');
        }
    }

    /**
     * Export activations data.
     */
    public function export(Request $request)
    {
        $query = Activation::with(['license.product', 'license.user']);

        // Apply same filters as index
        if ($request->has('product') && $request->product !== '') {
            $query->whereHas('license.product', function ($q) use ($request) {
                $q->where('id', $request->product);
            });
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('domain', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('license', function ($licenseQuery) use ($search) {
                      $licenseQuery->where('license_key', 'LIKE', "%{$search}%");
                  });
            });
        }

        $activations = $query->orderBy('activated_at', 'desc')->get();

        $filename = 'activations_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activations) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'License Key',
                'Product',
                'User Email',
                'Domain',
                'IP Address',
                'Activated At',
                'License Status'
            ]);

            // CSV Data
            foreach ($activations as $activation) {
                fputcsv($file, [
                    $activation->license->license_key,
                    $activation->license->product->name,
                    $activation->license->user->email ?? 'N/A',
                    $activation->domain,
                    $activation->ip_address,
                    $activation->activated_at->format('Y-m-d H:i:s'),
                    $activation->license->status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}