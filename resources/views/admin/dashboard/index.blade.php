@extends('layouts.adminlte.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_licenses'] }}</h3>
                <p>Total Licenses</p>
            </div>
            <div class="icon">
                <i class="ion ion-key"></i>
            </div>
            <a href="{{ route('admin.licenses.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['active_licenses'] }}</h3>
                <p>Active Licenses</p>
            </div>
            <div class="icon">
                <i class="ion ion-checkmark-circled"></i>
            </div>
            <a href="{{ route('admin.licenses.index', ['status' => 'active']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_activations'] }}</h3>
                <p>Total Activations</p>
            </div>
            <div class="icon">
                <i class="ion ion-earth"></i>
            </div>
            <a href="{{ route('admin.activations.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['expired_licenses'] }}</h3>
                <p>Expired Licenses</p>
            </div>
            <div class="icon">
                <i class="ion ion-alert-circled"></i>
            </div>
            <a href="{{ route('admin.licenses.index', ['status' => 'expired']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Recent Activations -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-globe mr-1"></i>
                    Recent Activations
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>License Key</th>
                                <th>Domain</th>
                                <th>Product</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentActivations as $activation)
                                <tr>
                                    <td>
                                        <code>{{ Str::limit($activation->license->license_key, 15) }}</code>
                                    </td>
                                    <td>{{ $activation->domain }}</td>
                                    <td>{{ $activation->license->product->name }}</td>
                                    <td>{{ $activation->activated_at->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent activations</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- License Status Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    License Status Distribution
                </h3>
            </div>
            <div class="card-body">
                <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </section>
    <!-- /.Left col -->

    <!-- Right col (fixed) -->
    <section class="col-lg-5 connectedSortable">
        <!-- Recent Licenses -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-key mr-1"></i>
                    Recent Licenses
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @forelse($recentLicenses as $license)
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <div>
                            <strong>{{ $license->product->name }}</strong><br>
                            <small class="text-muted">{{ $license->user->name ?? 'No User' }}</small><br>
                            <code class="small">{{ Str::limit($license->license_key, 20) }}</code>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-{{ $license->status === 'active' ? 'success' : ($license->status === 'expired' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($license->status) }}
                            </span><br>
                            <small class="text-muted">{{ $license->created_at->format('M d') }}</small>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No recent licenses</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-1"></i>
                    Quick Actions
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus mr-1"></i> Add Product
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.licenses.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-key mr-1"></i> Add License
                        </a>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6">
                        <a href="{{ route('admin.licenses.index', ['expired' => '1']) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-clock mr-1"></i> Check Expired
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.activations.export') }}" class="btn btn-info btn-block">
                            <i class="fas fa-download mr-1"></i> Export Data
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Activity -->
        @if(count($recentApiCalls) > 0)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i>
                    Recent API Calls
                </h3>
            </div>
            <div class="card-body">
                @foreach($recentApiCalls as $call)
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1 mb-1">
                        <div>
                            <small><code>{{ $call->endpoint }}</code></small><br>
                            <small class="text-muted">{{ $call->ip_address }}</small>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-{{ $call->status_code < 400 ? 'success' : 'danger' }}">
                                {{ $call->status_code }}
                            </span><br>
                            <small class="text-muted">{{ $call->created_at->format('H:i') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>
    <!-- /.Right col -->
</div>
<!-- /.row (main row) -->
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
$(function () {
    // Donut Chart
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData = {
        labels: [
            'Active',
            'Expired', 
            'Disabled',
            'Suspended'
        ],
        datasets: [
            {
                data: [
                    {{ $statusDistribution['active'] }},
                    {{ $statusDistribution['expired'] }},
                    {{ $statusDistribution['disabled'] }},
                    {{ $statusDistribution['suspended'] }}
                ],
                backgroundColor: ['#28a745', '#dc3545', '#6c757d', '#ffc107'],
                borderColor: ['#28a745', '#dc3545', '#6c757d', '#ffc107']
            }
        ]
    }
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
    new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
    })
})
</script>
@endpush