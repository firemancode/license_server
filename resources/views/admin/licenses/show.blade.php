@extends('layouts.adminlte.app')

@section('title', 'License Details')
@section('page-title', 'License: ' . $license->license_key)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.licenses.index') }}">Licenses</a></li>
    <li class="breadcrumb-item active">{{ $license->license_key }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">License Information</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.licenses.edit', $license) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">License Key:</dt>
                    <dd class="col-sm-9"><code>{{ $license->license_key }}</code></dd>

                    <dt class="col-sm-3">Product:</dt>
                    <dd class="col-sm-9">{{ $license->product->name }}</dd>

                    <dt class="col-sm-3">User:</dt>
                    <dd class="col-sm-9">
                        @if($license->user)
                            {{ $license->user->name }}<br>
                            <small class="text-muted">{{ $license->user->email }}</small>
                        @else
                            <span class="text-muted">No user assigned</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Status:</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-{{ $license->status === 'active' ? 'success' : ($license->status === 'expired' ? 'danger' : 'secondary') }}">
                            {{ ucfirst($license->status) }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">Max Activations:</dt>
                    <dd class="col-sm-9">{{ $license->max_activations ?? 1 }}</dd>

                    <dt class="col-sm-3">Current Activations:</dt>
                    <dd class="col-sm-9">{{ $license->activations->count() }}</dd>

                    <dt class="col-sm-3">Expires At:</dt>
                    <dd class="col-sm-9">
                        @if($license->expires_at)
                            <span class="@if($license->expires_at->isPast()) text-danger @endif">
                                {{ $license->expires_at->format('F d, Y \a\t H:i') }}
                            </span>
                            @if($license->expires_at->isPast())
                                <br><small class="text-danger">Expired {{ $license->expires_at->diffForHumans() }}</small>
                            @else
                                <br><small class="text-muted">{{ $license->expires_at->diffForHumans() }}</small>
                            @endif
                        @else
                            <span class="badge badge-success">Never expires</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9">{{ $license->created_at->format('F d, Y \a\t H:i') }}</dd>

                    <dt class="col-sm-3">Last Updated:</dt>
                    <dd class="col-sm-9">{{ $license->updated_at->format('F d, Y \a\t H:i') }}</dd>

                    @if($license->notes)
                    <dt class="col-sm-3">Notes:</dt>
                    <dd class="col-sm-9">{{ $license->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Activations -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activations ({{ $license->activations->count() }})</h3>
            </div>
            <div class="card-body">
                @if($license->activations->count() > 0)
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
                                @foreach($license->activations as $activation)
                                    <tr>
                                        <td><strong>{{ $activation->domain }}</strong></td>
                                        <td><code>{{ $activation->ip_address }}</code></td>
                                        <td>{{ $activation->activated_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.activations.revoke', $activation) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Revoke this activation?')"
                                                        title="Revoke">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No activations</h5>
                        <p class="text-muted">This license hasn't been activated on any domain yet.</p>
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
                <a href="{{ route('admin.licenses.edit', $license) }}" class="btn btn-warning btn-block">
                    <i class="fas fa-edit"></i> Edit License
                </a>
                
                @if($license->status === 'active')
                    <form method="POST" action="{{ route('admin.licenses.block', $license) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="fas fa-ban"></i> Block License
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.licenses.activate', $license) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> Activate License
                        </button>
                    </form>
                @endif

                @if($license->activations->count() > 0)
                    <form method="POST" action="{{ route('admin.licenses.reset_activations', $license) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-block" 
                                onclick="return confirm('Reset all activations for this license?')">
                            <i class="fas fa-redo"></i> Reset Activations
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.licenses.index') }}" class="btn btn-info btn-block mt-2">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- License API Testing -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">API Testing</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Verify License</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="test-domain" placeholder="domain.com">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" onclick="testLicense()">Test</button>
                        </div>
                    </div>
                </div>
                <div id="test-result" class="mt-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testLicense() {
    var domain = $('#test-domain').val();
    var licenseKey = '{{ $license->license_key }}';
    
    if (!domain) {
        $('#test-result').html('<div class="alert alert-warning">Please enter a domain</div>');
        return;
    }
    
    $('#test-result').html('<div class="alert alert-info">Testing...</div>');
    
    $.ajax({
        url: '{{ url("/api/verify-license") }}',
        method: 'POST',
        data: {
            license_key: licenseKey,
            domain: domain
        },
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            var alertClass = response.valid ? 'alert-success' : 'alert-danger';
            $('#test-result').html(
                '<div class="alert ' + alertClass + '">' +
                '<strong>Status:</strong> ' + response.status + '<br>' +
                '<strong>Message:</strong> ' + response.message +
                '</div>'
            );
        },
        error: function(xhr) {
            $('#test-result').html(
                '<div class="alert alert-danger">' +
                '<strong>Error:</strong> ' + xhr.statusText +
                '</div>'
            );
        }
    });
}
</script>
@endpush
 