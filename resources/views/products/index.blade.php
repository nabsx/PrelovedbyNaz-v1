@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header dengan Search -->
    <div class="mb-12">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-pink-500 bg-clip-text text-transparent mb-2">Produk Preloved</h1>
        <p class="text-gray-600 text-lg">Temukan Barang Preloved Berkualitas dengan Harga Terbaik</p>
        
        <!-- Search Bar -->
        <form action="{{ route('products.index') }}" method="GET" class="mt-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari produk..." 
                           value="{{ request('search') }}"
                           class="w-full px-6 py-3 border-2 border-pink-200 rounded-full focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all">
                </div>
                <button type="submit" 
                        class="btn-pink text-white px-8 py-3 rounded-full font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-search"></i>Cari
                </button>
                <a href="{{ route('products.index') }}" 
                   class="bg-gray-200 text-gray-700 px-8 py-3 rounded-full font-semibold hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-refresh"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24 border-2 border-pink-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Filter Produk</h3>
                
                <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                    <!-- Categories Filter -->
                    <div class="mb-8">
                        <h4 class="font-semibold text-gray-800 mb-4">Kategori</h4>
                        <div class="space-y-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="category" value="" 
                                       {{ !request('category') ? 'checked' : '' }}
                                       class="w-4 h-4 text-pink-500 accent-pink-500">
                                <span class="ml-3 text-gray-700">Semua Kategori</span>
                            </label>
                            @foreach($categories as $category)
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="category" value="{{ $category->slug }}"
                                       {{ request('category') == $category->slug ? 'checked' : '' }}
                                       class="w-4 h-4 text-pink-500 accent-pink-500">
                                <span class="ml-3 text-gray-700">{{ $category->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-8">
                        <h4 class="font-semibold text-gray-800 mb-4">Harga</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <input type="number" 
                                   name="min_price" 
                                   placeholder="Min" 
                                   value="{{ request('min_price') }}"
                                   class="px-3 py-2 border-2 border-pink-200 rounded-lg text-sm focus:ring-2 focus:ring-pink-500">
                            <input type="number" 
                                   name="max_price" 
                                   placeholder="Max" 
                                   value="{{ request('max_price') }}"
                                   class="px-3 py-2 border-2 border-pink-200 rounded-lg text-sm focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-8">
                        <h4 class="font-semibold text-gray-800 mb-4">Urutkan</h4>
                        <select name="sort"
                                class="w-full px-3 py-2 border-2 border-pink-200 rounded-lg text-sm focus:ring-2 focus:ring-pink-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full btn-pink text-white py-3 rounded-lg font-semibold">
                        Terapkan Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <!-- Results Info -->
            @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
            <div class="mb-6 p-4 bg-pink-50 rounded-lg border-l-4 border-pink-500">
                <p class="text-pink-700 font-medium">
                    Menampilkan {{ $products->total() }} hasil
                    @if(request('search')) untuk "<strong>{{ request('search') }}</strong>"@endif
                </p>
            </div>
            @endif

            @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                <div class="card-hover bg-white rounded-2xl shadow-md overflow-hidden border-2 border-pink-100">
                    <div class="h-48 bg-gradient-to-br from-pink-100 to-pink-50 relative overflow-hidden">
                        @if($product->stock == 0)
                        <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-10">
                            <span class="bg-red-500 text-white px-4 py-2 rounded-full text-sm font-semibold">Habis</span>
                        </div>
                        @endif
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                        @if($product->category)
                        <div class="absolute top-3 left-3">
                            <span class="bg-gradient-to-r from-pink-500 to-pink-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $product->category->name }}
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                        
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-pink-500 bg-clip-text text-transparent">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Stok: {{ $product->stock }}</span>
                        </div>
                        
                        <div class="flex gap-2">
                            <a href="{{ route('products.show', $product) }}" 
                               class="flex-1 bg-gray-100 text-gray-700 text-center py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                Detail
                            </a>
                            
                            @if($product->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" 
                                        class="w-full btn-pink text-white py-2 rounded-lg font-medium flex items-center justify-center gap-2">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-pink-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-search text-pink-400 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-gray-600 mb-8">Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center btn-pink text-white px-8 py-3 rounded-full font-semibold gap-2">
                    <i class="fas fa-refresh"></i>Reset Pencarian
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        if (!filterForm) return;

        const inputs = filterForm.querySelectorAll('input[type="radio"], select');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });
    });
</script>
@endsection
