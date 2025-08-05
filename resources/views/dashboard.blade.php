<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __("Welcome to License Server Dashboard!") }}</h3>
                    <p class="mb-4">{{ __("You're logged in!") }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.dashboard') }}" class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
                            <h4 class="font-semibold text-blue-800">Admin Dashboard</h4>
                            <p class="text-sm text-blue-600">Manage licenses and view statistics</p>
                        </a>
                        
                        <a href="{{ route('admin.licenses') }}" class="block p-4 bg-green-50 rounded-lg hover:bg-green-100">
                            <h4 class="font-semibold text-green-800">License Management</h4>
                            <p class="text-sm text-green-600">View and manage all licenses</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
