@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Produk Preloved</h1>
        <p class="text-gray-600 mt-2">Temukan barang preloved berkualitas dengan harga terbaik</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="h-48 bg-gray-200 relative">
                @if($product->stock == 0)
                <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm">Habis</span>
                </div>
                @endif
                <img src="https://via.placeholder.com/300x200?text=Preloved+Item" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover">
            </div>
            
            <div class="p-4">
                <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $product->name }}</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                
                <div class="flex justify-between items-center mb-3">
                    <span class="text-2xl font-bold text-pink-600">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="text-sm text-gray-500">Stok: {{ $product->stock }}</span>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('products.show', $product) }}" 
                       class="flex-1 bg-gray-100 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-200 transition-colors">
                        Detail
                    </a>
                    
                    @if($product->stock > 0)
                    <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" 
                                class="w-full bg-pink-500 text-white py-2 rounded-lg hover:bg-pink-600 transition-colors">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
@endsection