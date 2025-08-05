@extends('layouts.adminlte.app')

@section('title', 'Products')
@section('page-title', 'Products Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Products</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                @if($products->count() > 0)
                    <form id="bulk-action-form" method="POST" action="{{ route('admin.products.bulk_action') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="action" class="form-control" required>
                                        <option value="">Select Action...</option>
                                        <option value="delete">Delete Selected</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to perform this action on selected products?')">
                                            Apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Description</th>
                                        <th>Version</th>
                                        <th>Price</th>
                                        <th>Licenses</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="products[]" value="{{ $product->id }}" class="product-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $product->name }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $product->slug }}</code>
                                            </td>
                                            <td>
                                                {{ Str::limit($product->description ?? 'No description', 50) }}
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $product->version ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($product->price)
                                                    ${{ number_format($product->price, 2) }}
                                                @else
                                                    <span class="text-muted">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $product->licenses_count }}</span>
                                                @if($product->active_licenses_count > 0)
                                                    <span class="badge badge-success">{{ $product->active_licenses_count }} active</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->is_active ?? true)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.products.show', $product) }}" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($product->licenses_count == 0)
                                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                                    onclick="return confirm('Are you sure you want to delete this product?')" 
                                                                    title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-danger btn-sm" disabled title="Cannot delete - has licenses">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Get started by adding your first product.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Product
                        </a>
                    </div>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select All checkbox
    $('#select-all').change(function() {
        $('.product-checkbox').prop('checked', this.checked);
    });
    
    // Individual checkbox change
    $('.product-checkbox').change(function() {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        } else {
            var allChecked = $('.product-checkbox:checked').length === $('.product-checkbox').length;
            $('#select-all').prop('checked', allChecked);
        }
    });
});
</script>
@endpush