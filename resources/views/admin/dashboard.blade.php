<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-2xl font-bold text-blue-600">{{ $totalLicenses }}</div>
                        <div class="text-sm text-gray-600">Total Licenses</div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-2xl font-bold text-green-600">{{ $activeLicenses }}</div>
                        <div class="text-sm text-gray-600">Active Licenses</div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-2xl font-bold text-red-600">{{ $expiredLicenses }}</div>
                        <div class="text-sm text-gray-600">Expired Licenses</div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-2xl font-bold text-purple-600">{{ $totalActivations }}</div>
                        <div class="text-sm text-gray-600">Total Activations</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="flex gap-4">
                        <a href="{{ route('admin.licenses') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Manage Licenses
                        </a>
                        <form method="POST" action="{{ route('admin.licenses.bulk') }}" class="inline">
                            @csrf
                            <input type="hidden" name="operation" value="disable_expired">
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Disable Expired Licenses
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Activations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Recent Activations</h3>
                    @if($recentActivations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-left">License Key</th>
                                        <th class="px-4 py-2 text-left">Product</th>
                                        <th class="px-4 py-2 text-left">Domain</th>
                                        <th class="px-4 py-2 text-left">IP Address</th>
                                        <th class="px-4 py-2 text-left">Activated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivations as $activation)
                                        <tr class="border-b">
                                            <td class="px-4 py-2 font-mono text-sm">{{ $activation->license->license_key }}</td>
                                            <td class="px-4 py-2">{{ $activation->license->product->name }}</td>
                                            <td class="px-4 py-2">{{ $activation->domain }}</td>
                                            <td class="px-4 py-2">{{ $activation->ip_address }}</td>
                                            <td class="px-4 py-2">{{ $activation->activated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No recent activations found.</p>
                    @endif
                </div>
            </div>

            <!-- Licenses with Activations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Licenses with Active Domains</h3>
                    @if($licensesWithActivations->count() > 0)
                        <div class="space-y-4">
                            @foreach($licensesWithActivations as $license)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-mono text-sm font-semibold">{{ $license->license_key }}</h4>
                                            <p class="text-sm text-gray-600">{{ $license->product->name }}</p>
                                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                                @if($license->status === 'active') bg-green-100 text-green-800
                                                @elseif($license->status === 'expired') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($license->status) }}
                                            </span>
                                        </div>
                                        <form method="POST" action="{{ route('admin.licenses.reset_activations', $license) }}" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Reset all activations for this license?')" 
                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                Reset Activations
                                            </button>
                                        </form>
                                    </div>
                                    <div class="text-sm">
                                        <strong>Domains ({{ $license->activations->count() }}):</strong>
                                        @foreach($license->activations as $activation)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">
                                                {{ $activation->domain }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No licenses with activations found.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>