@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detail Produk</h1>
        <p class="text-gray-600 mt-2">Detail produk {{ $product->name }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <img src="https://via.placeholder.com/400x300?text=Preloved+Item" 
                         alt="{{ $product->name }}"
                         class="w-full h-64 object-cover rounded-lg">
                </div>
                
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $product->name }}</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Harga:</span>
                            <p class="text-3xl font-bold text-pink-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                        
                        <div>
                            <span class="text-gray-600">Stok:</span>
                            <p class="text-lg font-semibold">{{ $product->stock }} item</p>
                        </div>
                        
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        
                        <div>
                            <span class="text-gray-600">Slug:</span>
                            <p class="text-sm text-gray-900 font-mono">{{ $product->slug }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('admin.products.edit', $product) }}" 
                           class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Edit Produk
                        </a>
                        <a href="{{ route('admin.products.index') }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Produk</h3>
                <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
            </div>
        </div>
    </div>
</div>
@endsection