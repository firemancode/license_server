@extends('layouts.adminlte.app')

@section('title', 'Activation Details')
@section('page-title', 'Activation: ' . $activation->domain)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.activations.index') }}">Activations</a></li>
    <li class="breadcrumb-item active">{{ $activation->domain }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activation Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Domain:</dt>
                    <dd class="col-sm-9"><strong>{{ $activation->domain }}</strong></dd>

                    <dt class="col-sm-3">IP Address:</dt>
                    <dd class="col-sm-9"><code>{{ $activation->ip_address }}</code></dd>

                    <dt class="col-sm-3">Activated At:</dt>
                    <dd class="col-sm-9">
                        {{ $activation->activated_at->format('F d, Y \a\t H:i:s') }}<br>
                        <small class="text-muted">{{ $activation->activated_at->diffForHumans() }}</small>
                    </dd>

                    <dt class="col-sm-3">License Key:</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('admin.licenses.show', $activation->license) }}">
                            <code>{{ $activation->license->license_key }}</code>
                        </a>
                    </dd>

                    <dt class="col-sm-3">Product:</dt>
                    <dd class="col-sm-9">
                        <a href="{{ route('admin.products.show', $activation->license->product) }}">
                            {{ $activation->license->product->name }}
                        </a>
                    </dd>

                    <dt class="col-sm-3">License Owner:</dt>
                    <dd class="col-sm-9">
                        @if($activation->license->user)
                            {{ $activation->license->user->name }}<br>
                            <small class="text-muted">{{ $activation->license->user->email }}</small>
                        @else
                            <span class="text-muted">No user assigned</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">License Status:</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-{{ $activation->license->status === 'active' ? 'success' : ($activation->license->status === 'expired' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($activation->license->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">License Expires:</dt>
                    <dd class="col-sm-9">
                        @if($activation->license->expires_at)
                            <span class="@if($activation->license->expires_at->isPast()) text-danger @endif">
                                {{ $activation->license->expires_at->format('F d, Y \a\t H:i') }}
                            </span>
                            @if($activation->license->expires_at->isPast())
                                <br><small class="text-danger">Expired {{ $activation->license->expires_at->diffForHumans() }}</small>
                            @endif
                        @else
                            <span class="badge badge-success">Never expires</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9">{{ $activation->created_at->format('F d, Y \a\t H:i:s') }}</dd>

                    <dt class="col-sm-3">Last Updated:</dt>
                    <dd class="col-sm-9">{{ $activation->updated_at->format('F d, Y \a\t H:i:s') }}</dd>
                </dl>
            </div>
        </div>

        <!-- Related Activations -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Other Activations for this License</h3>
            </div>
            <div class="card-body">
                @if($activation->license->activations->count() > 1)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Domain</th>
                                    <th>IP Address</th>
                                    <th>Activated At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activation->license->activations as $otherActivation)
                                    <tr class="{{ $otherActivation->id === $activation->id ? 'table-primary' : '' }}">
                                        <td>
                                            <strong>{{ $otherActivation->domain }}</strong>
                                            @if($otherActivation->id === $activation->id)
                                                <span class="badge badge-info ml-1">Current</span>
                                            @endif
                                        </td>
                                        <td><code>{{ $otherActivation->ip_address }}</code></td>
                                        <td>{{ $otherActivation->activated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($otherActivation->id !== $activation->id)
                                                <a href="{{ route('admin.activations.show', $otherActivation) }}" 
                                                   class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.activations.revoke', $otherActivation) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            onclick="return confirm('Revoke this activation?')"
                                                            title="Revoke">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Current</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                        <h5 class="text-muted">Single Activation</h5>
                        <p class="text-muted">This is the only activation for this license.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.activations.revoke', $activation) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-block" 
                            onclick="return confirm('Are you sure you want to revoke this activation?')">
                        <i class="fas fa-times"></i> Revoke Activation
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.activations.block_domain', $activation) }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-block" 
                            onclick="return confirm('Block this domain and revoke activation?')">
                        <i class="fas fa-ban"></i> Block Domain
                    </button>
                </form>

                <a href="{{ route('admin.licenses.show', $activation->license) }}" class="btn btn-info btn-block">
                    <i class="fas fa-key"></i> View License
                </a>

                <a href="{{ route('admin.products.show', $activation->license->product) }}" class="btn btn-success btn-block">
                    <i class="fas fa-box"></i> View Product
                </a>

                <a href="{{ route('admin.activations.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statistics</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Duration</span>
                        <span class="info-box-number">{{ $activation->activated_at->diffForHumans(null, true) }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Activation Date</span>
                        <span class="info-box-number">{{ $activation->activated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Domain Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Domain Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Domain:</dt>
                    <dd class="col-sm-7">{{ $activation->domain }}</dd>

                    <dt class="col-sm-5">IP Address:</dt>
                    <dd class="col-sm-7"><code>{{ $activation->ip_address }}</code></dd>
                </dl>

                <hr>

                <h6>Quick Links:</h6>
                <div class="btn-group-vertical btn-group-sm w-100">
                    <a href="http://{{ $activation->domain }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Visit Domain
                    </a>
                    <a href="https://whois.net/{{ $activation->domain }}" target="_blank" class="btn btn-outline-info">
                        <i class="fas fa-search"></i> WHOIS Lookup
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
 