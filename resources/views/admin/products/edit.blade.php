@extends('layouts.adminlte.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product: ' . $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Edit {{ $product->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Information</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> View Product
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <form method="POST" action="{{ route('admin.products.update', $product) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                        <small class="form-text text-muted">URL-friendly version of the name. Only lowercase letters, numbers, and dashes.</small>
                        @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="version">Version</label>
                                <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                       id="version" name="version" value="{{ old('version', $product->version) }}" 
                                       placeholder="e.g., 1.0.0">
                                @error('version')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $product->price) }}" 
                                           placeholder="0.00">
                                </div>
                                <small class="form-text text-muted">Leave empty for free products</small>
                                @error('price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Product
                            </label>
                        </div>
                        <small class="form-text text-muted">Inactive products cannot have new licenses created</small>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    @if($product->licenses_count == 0)
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline float-right">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                                <i class="fas fa-trash"></i> Delete Product
                            </button>
                        </form>
                    @endif
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Statistics</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-key"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Licenses</span>
                        <span class="info-box-number">{{ $product->licenses_count ?? 0 }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Licenses</span>
                        <span class="info-box-number">{{ $product->active_licenses_count ?? 0 }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Created</span>
                        <span class="info-box-number">{{ $product->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($product->licenses_count > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Warning</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Cannot Delete</h5>
                    This product cannot be deleted because it has {{ $product->licenses_count }} associated license(s). 
                    Please delete or reassign the licenses first.
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.licenses.create', ['product_id' => $product->id]) }}" class="btn btn-success btn-block">
                    <i class="fas fa-plus"></i> Add License for this Product
                </a>
                <a href="{{ route('admin.licenses.index', ['product' => $product->id]) }}" class="btn btn-info btn-block">
                    <i class="fas fa-list"></i> View All Licenses
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name (but only if it matches the current slug)
    var originalSlug = $('#slug').val();
    $('#name').on('input', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with dashes
            .replace(/-+/g, '-'); // Replace multiple dashes with single dash
        
        if ($('#slug').val() === originalSlug || $('#slug').data('auto-generated')) {
            $('#slug').val(slug).data('auto-generated', true);
        }
    });

    // Mark slug as manually edited if user types in it
    $('#slug').on('input', function() {
        $(this).data('auto-generated', false);
    });
});
</script>
@endpush