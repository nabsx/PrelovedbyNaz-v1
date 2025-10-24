@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center space-x-2 text-sm text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-pink-600 transition">Home</a>
        <span class="text-pink-400">/</span>
        <a href="{{ route('products.index') }}" class="hover:text-pink-600 transition">Produk</a>
        <span class="text-pink-400">/</span>
        <span class="text-gray-900 font-medium">{{ $product->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Product Images -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-pink-100">
                <!-- Main Image -->
                <div class="relative bg-gradient-to-br from-pink-50 to-white aspect-square flex items-center justify-center overflow-hidden">
                    @if($product->stock == 0)
                    <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-10">
                        <span class="bg-red-500 text-white px-4 py-2 rounded-full text-lg font-semibold">Habis</span>
                    </div>
                    @endif
                    <img id="mainImage" 
                         src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                </div>

                <!-- Gallery Thumbnails -->
                @if($product->gallery_urls && count($product->gallery_urls) > 1)
                <div class="p-4 border-t-2 border-pink-100 bg-pink-50">
                    <div class="flex gap-2 overflow-x-auto">
                        <button onclick="changeImage('{{ $product->image_url }}')" 
                                class="flex-shrink-0 w-20 h-20 border-2 border-pink-500 rounded-lg overflow-hidden hover:border-pink-600 transition-colors">
                            <img src="{{ $product->image_url }}" alt="Main" class="w-full h-full object-cover">
                        </button>
                        @foreach($product->gallery_urls as $image)
                        <button onclick="changeImage('{{ $image }}')" 
                                class="flex-shrink-0 w-20 h-20 border-2 border-pink-200 rounded-lg overflow-hidden hover:border-pink-500 transition-colors">
                            <img src="{{ $image }}" alt="Gallery" class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4 border-2 border-pink-100">
                <!-- Category Badge -->
                @if($product->category)
                <div class="mb-4">
                    <span class="inline-block bg-gradient-to-r from-pink-200 to-pink-300 text-pink-700 px-4 py-2 rounded-full text-sm font-semibold">
                        {{ $product->category->name }}
                    </span>
                </div>
                @endif

                <!-- Product Name -->
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>

                <!-- Rating -->
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="ml-2 text-sm text-gray-600">(24 ulasan)</span>
                </div>

                <!-- Price -->
                <div class="mb-6 pb-6 border-b-2 border-pink-100">
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Stock Info -->
                <div class="mb-6 p-4 bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl border-2 border-pink-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700 font-medium">Stok Tersedia:</span>
                        <span class="text-lg font-bold text-gray-900">{{ $product->stock }} unit</span>
                    </div>
                    @if($product->stock > 0)
                    <div class="w-full bg-pink-300 rounded-full h-2">
                        <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-2 rounded-full" style="width: {{ min(($product->stock / 100) * 100, 100) }}%"></div>
                    </div>
                    @else
                    <div class="text-red-600 font-semibold">Produk Habis</div>
                    @endif
                </div>

                <!-- Add to Cart -->
                @if($product->stock > 0)
                <form id="addToCartForm" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                        <div class="flex items-center border-2 border-pink-200 rounded-xl bg-pink-50">
                            <button type="button" onclick="decreaseQty()" class="px-4 py-2 text-pink-600 hover:bg-pink-200 transition">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                   class="flex-1 text-center border-0 focus:ring-0 py-2 bg-transparent font-semibold">
                            <button type="button" onclick="increaseQty()" class="px-4 py-2 text-pink-600 hover:bg-pink-200 transition">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div id="alertContainer" class="mb-4"></div>

                    <button type="submit" id="addToCartBtn" class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-3 rounded-xl font-semibold hover:from-pink-600 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        Tambah ke Keranjang
                    </button>
                </form>
                @else
                <button disabled class="w-full bg-gray-400 text-white py-3 rounded-xl font-semibold cursor-not-allowed">
                    Produk Habis
                </button>
                @endif

                <!-- Share Buttons -->
                <div class="flex gap-2 mt-4">
                    <button class="flex-1 border-2 border-pink-300 text-pink-600 py-2 rounded-xl hover:bg-pink-50 transition-colors flex items-center justify-center gap-2 font-semibold">
                        <i class="fab fa-whatsapp"></i>
                        <span class="hidden sm:inline">Hubungi</span>
                    </button>
                    <button class="flex-1 border-2 border-pink-300 text-pink-600 py-2 rounded-xl hover:bg-pink-50 transition-colors flex items-center justify-center gap-2 font-semibold">
                        <i class="fas fa-share-alt"></i>
                        <span class="hidden sm:inline">Bagikan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-pink-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Deskripsi Produk</h2>
                <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">
                    {{ $product->description }}
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-pink-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Produk</h3>
                <div class="space-y-3">
                    <div class="flex justify-between pb-3 border-b border-pink-100">
                        <span class="text-gray-600">Kategori:</span>
                        <span class="font-medium text-gray-900">{{ $product->category->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-pink-100">
                        <span class="text-gray-600">Stok:</span>
                        <span class="font-medium text-gray-900">{{ $product->stock }} unit</span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-pink-100">
                        <span class="text-gray-600">Ditambahkan:</span>
                        <span class="font-medium text-gray-900">{{ $product->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium">
                            @if($product->is_active)
                            <span class="text-green-600 bg-green-100 px-3 py-1 rounded-full text-sm">Aktif</span>
                            @else
                            <span class="text-red-600 bg-red-100 px-3 py-1 rounded-full text-sm">Tidak Aktif</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Produk Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all border-2 border-pink-100 hover:border-pink-300 transform hover:scale-105">
                <div class="h-48 bg-gradient-to-br from-pink-50 to-white relative">
                    @if($relatedProduct->stock == 0)
                    <div class="absolute inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm">Habis</span>
                    </div>
                    @endif
                    <img src="{{ $relatedProduct->image_url }}" 
                         alt="{{ $relatedProduct->name }}" 
                         class="w-full h-full object-cover">
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ $relatedProduct->name }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($relatedProduct->description, 80) }}</p>
                    
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-2xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
                            Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                        </span>
                        <span class="text-sm text-gray-500 bg-pink-100 px-2 py-1 rounded-full">Stok: {{ $relatedProduct->stock }}</span>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('products.show', $relatedProduct) }}" 
                           class="flex-1 bg-pink-100 text-pink-600 text-center py-2 rounded-lg hover:bg-pink-200 transition-colors text-sm font-semibold">
                            Detail
                        </a>
                        
                        @if($relatedProduct->stock > 0)
                        <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-pink-500 to-pink-600 text-white py-2 rounded-lg hover:from-pink-600 hover:to-pink-700 transition-all text-sm font-semibold">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }

    function increaseQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }

    function decreaseQty() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }

    document.getElementById('addToCartForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = this;
        const btn = document.getElementById('addToCartBtn');
        const alertContainer = document.getElementById('alertContainer');
        const originalBtnText = btn.innerHTML;
        
        // Show loading state
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menambahkan...';
        alertContainer.innerHTML = '';
        
        try {
            const response = await fetch('{{ route("cart.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: form.product_id.value,
                    quantity: parseInt(form.quantity.value)
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Success alert
                alertContainer.innerHTML = `
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>${data.message}</span>
                    </div>
                `;
                form.quantity.value = '1';
                
                // Redirect to cart after 1.5 seconds
                setTimeout(() => {
                    window.location.href = '{{ route("cart.index") }}';
                }, 1500);
            } else {
                // Error alert
                alertContainer.innerHTML = `
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>${data.message}</span>
                    </div>
                `;
            }
        } catch (error) {
            alertContainer.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Terjadi kesalahan. Silakan coba lagi.</span>
                </div>
            `;
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnText;
        }
    });
</script>
@endsection
