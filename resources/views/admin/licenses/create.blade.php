@extends('layouts.adminlte.app')

@section('title', 'Add License')
@section('page-title', 'Add New License')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.licenses.index') }}">Licenses</a></li>
    <li class="breadcrumb-item active">Add License</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">License Information</h3>
            </div>
            <!-- /.card-header -->
            <form method="POST" action="{{ route('admin.licenses.store') }}">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">User <span class="text-danger">*</span></label>
                                <select class="form-control @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', request('user_id')) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_id">Product <span class="text-danger">*</span></label>
                                <select class="form-control @error('product_id') is-invalid @enderror" 
                                        id="product_id" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id', request('product_id')) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="license_key">License Key</label>
                        <input type="text" class="form-control @error('license_key') is-invalid @enderror" 
                               id="license_key" name="license_key" value="{{ old('license_key') }}" 
                               placeholder="Leave empty to auto-generate">
                        <small class="form-text text-muted">Format: XXXXX-XXXXX-XXXXX (will be auto-generated if empty)</small>
                        @error('license_key')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_activations">Max Activations</label>
                                <input type="number" min="1" max="100" 
                                       class="form-control @error('max_activations') is-invalid @enderror" 
                                       id="max_activations" name="max_activations" 
                                       value="{{ old('max_activations', 1) }}">
                                @error('max_activations')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expires_at">Expires At</label>
                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                               id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                        <small class="form-text text-muted">Leave empty for perpetual license</small>
                        @error('expires_at')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create License
                    </button>
                    <a href="{{ route('admin.licenses.index') }}" class="btn btn-secondary">
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
                <h3 class="card-title">Quick Info</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-key"></i> License Key</h5>
                    If left empty, a unique license key will be auto-generated in format: XXXXX-XXXXX-XXXXX
                </div>

                <div class="callout callout-warning">
                    <h5><i class="fas fa-users"></i> User Assignment</h5>
                    Select the user who will own this license. This determines access and ownership.
                </div>

                <div class="callout callout-success">
                    <h5><i class="fas fa-clock"></i> Expiration</h5>
                    Set an expiration date or leave empty for perpetual licenses.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate license key button
    $('#license_key').after('<button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="generate-key">Generate Key</button>');
    
    $('#generate-key').click(function() {
        var key = generateLicenseKey();
        $('#license_key').val(key);
    });
    
    function generateLicenseKey() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        var segments = [];
        
        for (var i = 0; i < 3; i++) {
            var segment = '';
            for (var j = 0; j < 5; j++) {
                segment += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            segments.push(segment);
        }
        
        return segments.join('-');
    }
});
</script>
@endpush
 