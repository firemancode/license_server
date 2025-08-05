<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Product;
use App\Models\Activation;
use App\Models\User;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Basic statistics
        $stats = [
            'total_licenses' => License::count(),
            'active_licenses' => License::where('status', 'active')->count(),
            'expired_licenses' => License::where('status', 'expired')->count(),
            'total_products' => Product::count(),
            'total_activations' => Activation::count(),
            'total_users' => User::count(),
        ];

        // Recent activity
        $recentActivations = Activation::with(['license.product', 'license.user'])
            ->orderBy('activated_at', 'desc')
            ->limit(10)
            ->get();

        $recentLicenses = License::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Charts data
        $dailyActivations = $this->getDailyActivationsData();
        $statusDistribution = $this->getLicenseStatusDistribution();
        $topProducts = $this->getTopProductsData();

        // Recent API calls (if ApiLog model exists)
        $recentApiCalls = [];
        if (class_exists(ApiLog::class)) {
            $recentApiCalls = ApiLog::orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('admin.dashboard.index', compact(
            'stats',
            'recentActivations',
            'recentLicenses',
            'dailyActivations',
            'statusDistribution',
            'topProducts',
            'recentApiCalls'
        ));
    }

    /**
     * Get daily activations data for the last 7 days.
     */
    private function getDailyActivationsData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Activation::whereDate('activated_at', $date)->count();
            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }
        return $data;
    }

    /**
     * Get license status distribution.
     */
    private function getLicenseStatusDistribution(): array
    {
        return [
            'active' => License::where('status', 'active')->count(),
            'expired' => License::where('status', 'expired')->count(),
            'disabled' => License::where('status', 'disabled')->count(),
            'suspended' => License::where('status', 'suspended')->count(),
        ];
    }

    /**
     * Get top products by license count.
     */
    private function getTopProductsData(): array
    {
        return Product::withCount('licenses')
            ->orderBy('licenses_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'count' => $product->licenses_count
                ];
            })
            ->toArray();
    }
}