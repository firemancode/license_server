<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = Product::withCount(['licenses', 'licenses as active_licenses_count' => function ($query) {
            $query->where('status', 'active');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $product = Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product '{$product->name}' created successfully!");
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->loadCount(['licenses', 'licenses as active_licenses_count' => function ($query) {
            $query->where('status', 'active');
        }]);

        $licenses = $product->licenses()
            ->with(['user', 'activations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.products.show', compact('product', 'licenses'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        
        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique (excluding current product)
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product '{$product->name}' updated successfully!");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $productName = $product->name;
        $licensesCount = $product->licenses()->count();

        if ($licensesCount > 0) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', "Cannot delete product '{$productName}' because it has {$licensesCount} associated license(s). Please delete or reassign the licenses first.");
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Product '{$productName}' deleted successfully!");
    }

    /**
     * Bulk actions for products.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:delete',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id'
        ]);

        $action = $request->action;
        $productIds = $request->products;

        switch ($action) {
            case 'delete':
                // Check if any products have licenses
                $productsWithLicenses = Product::whereIn('id', $productIds)
                    ->has('licenses')
                    ->pluck('name')
                    ->toArray();

                if (!empty($productsWithLicenses)) {
                    return redirect()
                        ->route('admin.products.index')
                        ->with('error', 'Cannot delete products with existing licenses: ' . implode(', ', $productsWithLicenses));
                }

                $deletedCount = Product::whereIn('id', $productIds)->delete();
                
                return redirect()
                    ->route('admin.products.index')
                    ->with('success', "Successfully deleted {$deletedCount} product(s).");

            default:
                return redirect()
                    ->route('admin.products.index')
                    ->with('error', 'Invalid action.');
        }
    }
}