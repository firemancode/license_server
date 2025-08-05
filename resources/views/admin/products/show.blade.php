@extends('layouts.adminlte.app')

@section('title', 'Product Details')
@section('page-title', 'Product: ' . $product->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Information</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name:</dt>
                    <dd class="col-sm-9">{{ $product->name }}</dd>

                    <dt class="col-sm-3">Slug:</dt>
                    <dd class="col-sm-9"><code>{{ $product->slug }}</code></dd>

                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">{{ $product->description ?: 'No description provided' }}</dd>

                    <dt class="col-sm-3">Version:</dt>
                    <dd class="col-sm-9">
                        @if($product->version)
                            <span class="badge badge-info">{{ $product->version }}</span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Price:</dt>
                    <dd class="col-sm-9">
                        @if($product->price)
                            <strong>${{ number_format($product->price, 2) }}</strong>
                        @else
                            <span class="badge badge-success">Free</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        @if($product->is_active ?? true)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9">{{ $product->created_at->format('F d, Y \a\t H:i') }}</dd>

                    <dt class="col-sm-3">Last Updated:</dt>
                    <dd class="col-sm-9">{{ $product->updated_at->format('F d, Y \a\t H:i') }}</dd>
                </dl>
            </div>
        </div>

        <!-- Associated Licenses -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Associated Licenses ({{ $licenses->total() }})</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.licenses.create', ['product_id' => $product->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add License
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($licenses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>License Key</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                    <th>Activations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($licenses as $license)
                                    <tr>
                                        <td>
                                            <code>{{ $license->license_key }}</code>
                                        </td>
                                        <td>
                                            @if($license->user)
                                                {{ $license->user->name }}<br>
                                                <small class="text-muted">{{ $license->user->email }}</small>
                                            @else
                                                <span class="text-muted">No user assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $license->status === 'active' ? 'success' : ($license->status === 'expired' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst($license->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($license->expires_at)
                                                <span class="@if($license->expires_at->isPast()) text-danger @endif">
                                                    {{ $license->expires_at->format('M d, Y') }}
                                                </span>
                                                @if($license->expires_at->isPast())
                                                    <br><small class="text-danger">Expired</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $license->activations->count() }}</span>
                                            @if($license->activations->count() > 0)
                                                <br>
                                                @foreach($license->activations as $activation)
                                                    <small class="text-muted d-block">{{ $activation->domain }}</small>
                                                @endforeach
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
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $licenses->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No licenses found</h5>
                        <p class="text-muted">This product doesn't have any licenses yet.</p>
                        <a href="{{ route('admin.licenses.create', ['product_id' => $product->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create First License
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-key"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Licenses</span>
                        <span class="info-box-number">{{ $product->licenses_count }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Licenses</span>
                        <span class="info-box-number">{{ $product->active_licenses_count }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-globe"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Activations</span>
                        <span class="info-box-number">{{ $product->licenses->sum(fn($license) => $license->activations->count()) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-block">
                    <i class="fas fa-edit"></i> Edit Product
                </a>
                <a href="{{ route('admin.licenses.create', ['product_id' => $product->id]) }}" class="btn btn-success btn-block">
                    <i class="fas fa-plus"></i> Add License
                </a>
                <a href="{{ route('admin.licenses.index', ['product' => $product->id]) }}" class="btn btn-info btn-block">
                    <i class="fas fa-list"></i> View All Licenses
                </a>
                @if($product->licenses_count == 0)
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block" 
                                onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            <i class="fas fa-trash"></i> Delete Product
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
 