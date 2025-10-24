@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Kelola toko Prelovedbynaz Anda</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-pink-100 rounded-lg">
                    <i class="fas fa-box text-pink-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Produk</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $products }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $transactions }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total User</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.products.create') }}" 
               class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                <i class="fas fa-plus text-pink-600 text-2xl mb-2"></i>
                <span class="font-medium">Tambah Produk</span>
                <span class="text-sm text-gray-500 mt-1">+ Upload Gambar</span>
            </a>
            
            <a href="{{ route('admin.products.index') }}" 
               class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                <i class="fas fa-list text-pink-600 text-2xl mb-2"></i>
                <span class="font-medium">Kelola Produk</span>
                <span class="text-sm text-gray-500 mt-1">Edit & Hapus</span>
            </a>
            
            <a href="{{ route('admin.categories.index') }}" 
               class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                <i class="fas fa-tags text-pink-600 text-2xl mb-2"></i>
                <span class="font-medium">Kelola Kategori</span>
                <span class="text-sm text-gray-500 mt-1">Tambah & Edit</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" 
               class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                <i class="fas fa-users text-pink-600 text-2xl mb-2"></i>
                <span class="font-medium">Kelola User</span>
                <span class="text-sm text-gray-500 mt-1">Lihat semua user</span>
            </a>
        </div>
    </div>

    <!-- Recent Products & Low Stock Alert -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Products -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Produk Terbaru</h2>
            <div class="space-y-4">
                @forelse($recentProducts as $product)
                <div class="flex items-center space-x-3 p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}"
                         class="w-12 h-12 object-cover rounded">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ Str::limit($product->name, 30) }}</h4>
                        <p class="text-sm text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full 
                        {{ $product->stock < 5 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        Stok: {{ $product->stock }}
                    </span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">Belum ada produk</p>
                @endforelse
            </div>
            <a href="{{ route('admin.products.index') }}" 
               class="block text-center mt-4 text-pink-600 hover:text-pink-700 font-medium">
                Lihat Semua Produk →
            </a>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Stok Menipis</h2>
            <div class="space-y-3">
                @forelse($lowStockProducts as $product)
                <div class="flex justify-between items-center p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div>
                        <h4 class="font-medium text-red-900">{{ $product->name }}</h4>
                        <p class="text-sm text-red-700">Stok tersisa: {{ $product->stock }}</p>
                    </div>
                    <a href="{{ route('admin.products.edit', $product) }}" 
                       class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                        Restock
                    </a>
                </div>
                @empty
                <p class="text-green-500 text-center py-4">✅ Semua stok aman</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection