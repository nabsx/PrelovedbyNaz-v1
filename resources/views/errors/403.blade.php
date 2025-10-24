@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="mx-auto w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-lock text-red-600 text-3xl"></i>
        </div>
        
        <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Access Denied</h2>
        
        <p class="text-gray-600 mb-8">
            You don't have permission to access this page. 
            This area is restricted to administrators only.
        </p>

        <div class="space-y-4">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center px-6 py-3 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                <i class="fas fa-home mr-2"></i>
                Back to Home
            </a>
            
            @auth
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors ml-4">
                <i class="fas fa-shopping-bag mr-2"></i>
                Continue Shopping
            </a>
            @endauth
        </div>
    </div>
</div>
@endsection