@extends('layouts.adminlte.app')

@section('title', 'Add Product')
@section('page-title', 'Add New Product')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Add Product</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Information</h3>
            </div>
            <!-- /.card-header -->
            <form method="POST" action="{{ route('admin.products.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug') }}" 
                               placeholder="Leave empty to auto-generate from name">
                        <small class="form-text text-muted">URL-friendly version of the name. Only lowercase letters, numbers, and dashes.</small>
                        @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="version">Version</label>
                                <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                       id="version" name="version" value="{{ old('version') }}" 
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
                                           id="price" name="price" value="{{ old('price') }}" 
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
                                   {{ old('is_active', true) ? 'checked' : '' }}>
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
                        <i class="fas fa-save"></i> Create Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tips</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Product Name</h5>
                    Choose a clear, descriptive name for your product. This will be visible to customers.
                </div>

                <div class="callout callout-warning">
                    <h5><i class="fas fa-link"></i> Slug</h5>
                    The slug is used in URLs and API calls. If left empty, it will be auto-generated from the product name.
                </div>

                <div class="callout callout-success">
                    <h5><i class="fas fa-dollar-sign"></i> Pricing</h5>
                    Set a price for paid products, or leave empty for free products. You can change this later.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('input', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-') // Replace spaces with dashes
            .replace(/-+/g, '-'); // Replace multiple dashes with single dash
        
        if ($('#slug').val() === '' || $('#slug').data('auto-generated')) {
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