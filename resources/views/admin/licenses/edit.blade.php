@extends('layouts.adminlte.app')

@section('title', 'Edit License')
@section('page-title', 'Edit License: ' . $license->license_key)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.licenses.index') }}">Licenses</a></li>
    <li class="breadcrumb-item active">Edit {{ $license->license_key }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">License Information</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.licenses.show', $license) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> View License
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <form method="POST" action="{{ route('admin.licenses.update', $license) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">User <span class="text-danger">*</span></label>
                                <select class="form-control @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $license->user_id) == $user->id ? 'selected' : '' }}>
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
                                        <option value="{{ $product->id }}" {{ old('product_id', $license->product_id) == $product->id ? 'selected' : '' }}>
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
                        <label for="license_key">License Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('license_key') is-invalid @enderror" 
                               id="license_key" name="license_key" value="{{ old('license_key', $license->license_key) }}" required>
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
                                    <option value="active" {{ old('status', $license->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $license->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="expired" {{ old('status', $license->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="disabled" {{ old('status', $license->status) == 'disabled' ? 'selected' : '' }}>Disabled</option>
                                    <option value="suspended" {{ old('status', $license->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                                       value="{{ old('max_activations', $license->max_activations ?? 1) }}">
                                @error('max_activations')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expires_at">Expires At</label>
                        <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                               id="expires_at" name="expires_at" 
                               value="{{ old('expires_at', $license->expires_at ? $license->expires_at->format('Y-m-d\TH:i') : '') }}">
                        <small class="form-text text-muted">Leave empty for perpetual license</small>
                        @error('expires_at')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes', $license->notes) }}</textarea>
                        @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update License
                    </button>
                    <a href="{{ route('admin.licenses.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('admin.licenses.show', $license) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    
                    <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}" class="d-inline float-right">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this license? This will also delete all its activations.')">
                            <i class="fas fa-trash"></i> Delete License
                        </button>
                    </form>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Current Status</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-{{ $license->status === 'active' ? 'success' : 'danger' }}">
                        <i class="fas fa-{{ $license->status === 'active' ? 'check' : 'times' }}"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Status</span>
                        <span class="info-box-number">{{ ucfirst($license->status) }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-globe"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Activations</span>
                        <span class="info-box-number">{{ $license->activations->count() }} / {{ $license->max_activations ?? 1 }}</span>
                    </div>
                </div>

                @if($license->expires_at)
                <div class="info-box">
                    <span class="info-box-icon bg-{{ $license->expires_at->isPast() ? 'danger' : 'warning' }}">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Expires</span>
                        <span class="info-box-number">{{ $license->expires_at->format('M d, Y') }}</span>
                        <div class="progress-description">
                            {{ $license->expires_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                @if($license->status === 'active')
                    <form method="POST" action="{{ route('admin.licenses.block', $license) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-ban"></i> Block License
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.licenses.activate', $license) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> Activate License
                        </button>
                    </form>
                @endif

                @if($license->activations->count() > 0)
                    <form method="POST" action="{{ route('admin.licenses.reset_activations', $license) }}" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-info btn-block" 
                                onclick="return confirm('Reset all activations for this license?')">
                            <i class="fas fa-redo"></i> Reset Activations ({{ $license->activations->count() }})
                        </button>
                    </form>
                @endif

                <div class="mt-3">
                    <h6>Current Activations:</h6>
                    @if($license->activations->count() > 0)
                        @foreach($license->activations as $activation)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small>{{ $activation->domain }}</small>
                                <form method="POST" action="{{ route('admin.activations.revoke', $activation) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs" 
                                            onclick="return confirm('Revoke this activation?')"
                                            title="Revoke">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    @else
                        <small class="text-muted">No activations</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
 