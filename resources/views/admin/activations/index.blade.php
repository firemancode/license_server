@extends('layouts.adminlte.app')

@section('title', 'Activations')
@section('page-title', 'Activation Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Activations</li>
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
                <form method="GET" action="{{ route('admin.activations.index') }}">
                    <div class="row">
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
                                <label>License Status</label>
                                <select name="license_status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach($licenseStatuses as $status)
                                        <option value="{{ $status }}" {{ request('license_status') === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Domain, IP, or license key..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.activations.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activations List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Activations ({{ $activations->total() }})</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.activations.export', request()->query()) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($activations->count() > 0)
                    <form id="bulk-action-form" method="POST" action="{{ route('admin.activations.bulk_action') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="action" class="form-control" required>
                                        <option value="">Select Action...</option>
                                        <option value="revoke">Revoke Selected</option>
                                        <option value="block_domains">Block Domains & Revoke</option>
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
                                        <th>Domain</th>
                                        <th>IP Address</th>
                                        <th>Activated At</th>
                                        <th>License Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activations as $activation)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="activations[]" value="{{ $activation->id }}" class="activation-checkbox">
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.licenses.show', $activation->license) }}">
                                                    <code>{{ $activation->license->license_key }}</code>
                                                </a>
                                            </td>
                                            <td>
                                                <strong>{{ $activation->license->product->name }}</strong>
                                            </td>
                                            <td>
                                                @if($activation->license->user)
                                                    {{ $activation->license->user->name }}<br>
                                                    <small class="text-muted">{{ $activation->license->user->email }}</small>
                                                @else
                                                    <span class="text-muted">No user</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $activation->domain }}</strong>
                                            </td>
                                            <td>
                                                <code>{{ $activation->ip_address }}</code>
                                            </td>
                                            <td>
                                                {{ $activation->activated_at->format('M d, Y H:i') }}<br>
                                                <small class="text-muted">{{ $activation->activated_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $activation->license->status === 'active' ? 'success' : ($activation->license->status === 'expired' ? 'danger' : 'secondary') }}">
                                                    {{ ucfirst($activation->license->status) }}
                                                </span>
                                                @if($activation->license->expires_at && $activation->license->expires_at->isPast())
                                                    <br><small class="text-danger">Expired</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.activations.show', $activation) }}" 
                                                       class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.activations.revoke', $activation) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm" title="Revoke"
                                                                onclick="return confirm('Revoke this activation?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.activations.block_domain', $activation) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Block Domain"
                                                                onclick="return confirm('Block this domain and revoke activation?')">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </form>
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
                        {{ $activations->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No activations found</h5>
                        <p class="text-muted">No license activations match your current filter criteria.</p>
                        <a href="{{ route('admin.activations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-filter"></i> Clear Filters
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
        $('.activation-checkbox').prop('checked', this.checked);
    });
    
    // Individual checkbox change
    $('.activation-checkbox').change(function() {
        if (!this.checked) {
            $('#select-all').prop('checked', false);
        } else {
            var allChecked = $('.activation-checkbox:checked').length === $('.activation-checkbox').length;
            $('#select-all').prop('checked', allChecked);
        }
    });
});
</script>
@endpush
 