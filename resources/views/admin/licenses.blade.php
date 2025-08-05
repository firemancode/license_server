<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('License Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Bulk Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Bulk Actions</h3>
                    <div class="flex gap-4">
                        <form method="POST" action="{{ route('admin.licenses.bulk') }}" class="inline">
                            @csrf
                            <input type="hidden" name="operation" value="disable_expired">
                            <button type="submit" onclick="return confirm('Disable all expired licenses?')" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Disable All Expired
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Licenses Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">All Licenses</h3>
                    
                    @if($licenses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-2 text-left">License Key</th>
                                        <th class="px-4 py-2 text-left">User</th>
                                        <th class="px-4 py-2 text-left">Product</th>
                                        <th class="px-4 py-2 text-left">Status</th>
                                        <th class="px-4 py-2 text-left">Expires At</th>
                                        <th class="px-4 py-2 text-left">Activations</th>
                                        <th class="px-4 py-2 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenses as $license)
                                        <tr class="border-b">
                                            <td class="px-4 py-2 font-mono text-sm">{{ $license->license_key }}</td>
                                            <td class="px-4 py-2">{{ $license->user->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $license->product->name }}</td>
                                            <td class="px-4 py-2">
                                                <span class="inline-block px-2 py-1 text-xs rounded-full 
                                                    @if($license->status === 'active') bg-green-100 text-green-800
                                                    @elseif($license->status === 'expired') bg-red-100 text-red-800
                                                    @elseif($license->status === 'disabled') bg-gray-100 text-gray-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($license->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">
                                                @if($license->expires_at)
                                                    <span class="@if($license->expires_at->isPast()) text-red-600 @endif">
                                                        {{ $license->expires_at->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    Never
                                                @endif
                                            </td>
                                            <td class="px-4 py-2">
                                                <div class="text-sm">
                                                    <strong>{{ $license->activations->count() }}</strong> domain(s)
                                                    @if($license->activations->count() > 0)
                                                        <div class="text-xs text-gray-600 mt-1">
                                                            @foreach($license->activations as $activation)
                                                                <div>{{ $activation->domain }}</div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-2">
                                                <div class="flex gap-2">
                                                    <!-- Reset Activations -->
                                                    @if($license->activations->count() > 0)
                                                        <form method="POST" action="{{ route('admin.licenses.reset_activations', $license) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" onclick="return confirm('Reset all activations for this license?')" 
                                                                    class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                                Reset
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Change Status -->
                                                    <form method="POST" action="{{ route('admin.licenses.update_status', $license) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status" onchange="this.form.submit()" class="text-xs border rounded px-1 py-1">
                                                            <option value="">Change Status...</option>
                                                            <option value="active" @if($license->status === 'active') selected @endif>Active</option>
                                                            <option value="expired" @if($license->status === 'expired') selected @endif>Expired</option>
                                                            <option value="disabled" @if($license->status === 'disabled') selected @endif>Disabled</option>
                                                            <option value="suspended" @if($license->status === 'suspended') selected @endif>Suspended</option>
                                                        </select>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $licenses->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No licenses found.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>