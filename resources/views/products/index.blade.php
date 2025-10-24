@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header dengan Search -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Produk Preloved</h1>
        <p class="text-gray-600 mt-2">Temukan barang preloved berkualitas dengan harga terbaik</p>
        
        <!-- Search Bar -->
        <form action="{{ route('products.index') }}" method="GET" class="mt-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           placeholder="Cari produk..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
                <button type="submit" 
                        class="bg-pink-500 text-white px-6 py-3 rounded-lg hover:bg-pink-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <a href="{{ route('products.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors flex items-center">
                    <i class="fas fa-refresh mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Produk</h3>
                
                <!-- Categories Filter -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Kategori</h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="category" value="" 
                                   {{ !request('category') ? 'checked' : '' }}
                                   onchange="this.form.submit()"
                                   class="text-pink-500 focus:ring-pink-500">
                            <span class="ml-2 text-sm text-gray-600">Semua Kategori</span>
                        </label>
                        @foreach($categories as $category)
                        <label class="flex items-center">
                            <input type="radio" name="category" value="{{ $category->slug }}"
                                   {{ request('category') == $category->slug ? 'checked' : '' }}
                                   onchange="this.form.submit()"
                                   class="text-pink-500 focus:ring-pink-500">
                            <span class="ml-2 text-sm text-gray-600">{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Harga</h4>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" 
                               name="min_price" 
                               placeholder="Min" 
                               value="{{ request('min_price') }}"
                               class="px-3 py-2 border border-gray-300 rounded text-sm">
                        <input type="number" 
                               name="max_price" 
                               placeholder="Max" 
                               value="{{ request('max_price') }}"
                               class="px-3 py-2 border border-gray-300 rounded text-sm">
                    </div>
                </div>

                <!-- Sort Options -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">Urutkan</h4>
                    <select name="sort" onchange="this.form.submit()" 
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                    </select>
                </div>

                <button type="submit" 
                        class="w-full bg-pink-500 text-white py-2 rounded-lg hover:bg-pink-600 transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </form>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <!-- Results Info -->
            @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price']))
            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-blue-700">
                    Menampilkan {{ $products->total() }} hasil
                    @if(request('search')) untuk "<strong>{{ request('search') }}</strong>"@endif
                </p>
            </div>
            @endif

            @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gray-200 relative">
                        @if($product->stock == 0)
                        <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm">Habis</span>
                        </div>
                        @endif
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover">
                        @if($product->category)
                        <div class="absolute top-2 left-2">
                            <span class="bg-pink-500 text-white px-2 py-1 rounded-full text-xs">
                                {{ $product->category->name }}
                            </span>
                        </div>
                        @endif
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
                {{ $products->appends(request()->query())->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                <p class="text-gray-600 mb-6">Coba ubah kata kunci pencarian atau filter yang digunakan</p>
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Reset Pencarian
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Auto submit form when filters change
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form');
        const inputs = filterForm.querySelectorAll('input[type="radio"], select');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.type === 'radio' || this.tagName === 'SELECT') {
                    filterForm.submit();
                }
            });
        });
    });
</script>
@endsection