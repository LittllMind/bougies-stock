@extends('layouts.app')

@section('content')
<!-- Page Header -->
@if (isset($header))
    <header class="bg-gradient-to-r from-amber-600 to-orange-500 shadow-lg">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </header>
@endif

<!-- Flash Messages -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if (session('status'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded" role="alert">
            <p>{{ session('status') }}</p>
        </div>
    @endif
</div>

<div class="flex min-h-screen bg-amber-50">
    
    @php
        // Pass pendingOrders count to sidebar
        $pendingOrders = isset($pendingOrders) ? $pendingOrders : \App\Models\Order::where('user_id', Auth::id())
            ->where('statut', 'pending')
            ->count();
    @endphp
    
    @include('client.partials.sidebar')

    <!-- Main Content -->
    <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('client-content')
        </div>
    </div>
</div>
@endsection