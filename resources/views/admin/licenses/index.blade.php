@extends('layouts.adminlte.app')

@section('title', 'Licenses')
@section('page-title', 'License Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Licenses</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filters</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.licenses.index') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="product" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="License key or user..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.licenses.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="expired" value="1" 
                                       {{ request('expired') ? 'checked' : '' }}>
                                <label class="form-check-label">Show only expired (but not disabled)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Licenses List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Licenses ({{ $licenses->total() }})</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.licenses.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add License
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($licenses->count() > 0)
                    <form id="bulk-action-form" method="POST" action="{{ route('admin.licenses.bulk_action') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="action" class="form-control" required>
                                        <option value="">Select Action...</option>
                                        <option value="activate">Activate Selected</option>
                                        <option value="block">Block Selected</option>
                                        <option value="reset_activations">Reset Activations</option>
                                        <option value="delete">Delete Selected</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
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
                                        <th>License Key</th>
                                        <th>Product</th>
                                        <th>User</th>
                                        <th>Status</th>
                                        <th>Expires At</th>
                                        <th>Activations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenses as $license)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="licenses[]" value="{{ $license->id }}" class="license-checkbox">
                                            </td>
                                            <td>
                                                <code>{{ $license->license_key }}</code>
                                                @if($license->notes)
                                                    <br><small class="text-muted">{{ Str::limit($license->notes, 30) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $license->product->name }}</strong>
                                            </td>
                                            <td>
                                                @if($license->user)
                                                    {{ $license->user->name }}<br>
                                                    <small class="text-muted">{{ $license->user->email }}</small>
                                                @else
                                                    <span class="text-muted">No user</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $license->status === 'active' ? 'success' : ($license->status === 'expired' ? 'danger' : 'secondary') }}">
                                                    {{ ucfirst($license->status) }}
                                                </span>
                                                @if($license->expires_at && $license->expires_at->isPast() && $license->status === 'active')
                                                    <br><small class="text-danger">Expired!</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($license->expires_at)
                                                    <span class="@if($license->expires_at->isPast()) text-danger @endif">
                                                        {{ $license->expires_at->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $license->activations->count() }}</span>
                                                @if($license->activations->count() > 0)
                                                    <br>
                                                    @foreach($license->activations->take(2) as $activation)
                                                        <small class="text-muted d-block">{{ $activation->domain }}</small>
                                                    @endforeach
                                                    @if($license->activations->count() > 2)
                                                        <small class="text-muted">+{{ $license->activations->count() - 2 }} more</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.licenses.show', $license) }}" 
                                                       class="btn btn-info btn-sm" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.licenses.edit', $license) }}" 
                                                       class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($license->status === 'active')
                                                        <form method="POST" action="{{ route('admin.licenses.block', $license) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-secondary btn-sm" title="Block">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.licenses.activate', $license) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" title="Activate">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($license->activations->count() > 0)
                                                        <form method="POST" action="{{ route('admin.licenses.reset_activations', $license) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning btn-sm" title="Reset Activations" 
                                                                    onclick="return confirm('Reset all activations?')">
                                                                <i class="fas fa-redo"></i>
                                                            </button>
                                                        </form>
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
                        {{ $licenses->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No licenses found</h5>
                        <p class="text-muted">Create your first license to get started.</p>
                        <a href="{{ route('admin.licenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add License
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select All checkbox
    $('#select-all').change(function() {
        $('.license-checkbox').prop('checked', this.checked);
    });
    
    // Individual checkbox change
    $('.license-checkbox').change(function() {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        } else {
            var allChecked = $('.license-checkbox:checked').length === $('.license-checkbox').length;
            $('#select-all').prop('checked', allChecked);
        }
    });
});
</script>
@endpush
 