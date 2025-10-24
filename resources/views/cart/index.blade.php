@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Keranjang Belanja</h1>
        <p class="text-gray-600 mt-2">Review produk yang akan Anda beli</p>
    </div>

    @if($cartItems->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="space-y-6">
                    @foreach($cartItems as $cartItem)
                    <div class="flex items-center space-x-4 border-b border-gray-200 pb-6 last:border-b-0">
                        <div class="flex-shrink-0">
                            <img src="https://via.placeholder.com/100x100?text=Preloved" 
                                 alt="{{ $cartItem->product->name }}"
                                 class="w-20 h-20 object-cover rounded-lg">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $cartItem->product->name }}
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">
                                {{ Str::limit($cartItem->product->description, 50) }}
                            </p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-2xl font-bold text-pink-600">
                                    Rp {{ number_format($cartItem->product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Stok: {{ $cartItem->product->stock }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <form action="{{ route('cart.update', $cartItem) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                @method('PUT')
                                <button type="button" 
                                        onclick="this.parentNode.querySelector('input[type=number]').stepDown()"
                                        class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-100">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                
                                <input type="number" 
                                       name="quantity" 
                                       value="{{ $cartItem->quantity }}" 
                                       min="1" 
                                       max="{{ $cartItem->product->stock }}"
                                       class="w-16 text-center border border-gray-300 rounded-lg py-1">
                                
                                <button type="button" 
                                        onclick="this.parentNode.querySelector('input[type=number]').stepUp()"
                                        class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-100">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                                
                                <button type="submit" 
                                        class="ml-2 px-3 py-1 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                    Update
                                </button>
                            </form>

                            <form action="{{ route('cart.destroy', $cartItem) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-2xl font-bold text-gray-900">Total:</span>
                        <span class="text-2xl font-bold text-pink-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between space-x-4">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Kosongkan Keranjang
                            </button>
                        </form>

                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-8 py-3 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors font-semibold">
                                <i class="fas fa-credit-card mr-2"></i>Checkout Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                <p class="text-yellow-700 text-sm">
                    <strong>Perhatian:</strong> Item di keranjang akan otomatis dihapus setelah 20 menit tidak ada aktivitas.
                </p>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-shopping-cart text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Keranjang Kosong</h3>
            <p class="text-gray-600 mb-6">Belum ada produk yang ditambahkan ke keranjang</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Mulai Belanja
            </a>
        </div>
    @endif
</div>

<script>
    // Auto update quantity when changed
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('input[name="quantity"]');
        
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Validate min and max
                if (this.value < 1) this.value = 1;
                if (this.value > parseInt(this.max)) this.value = this.max;
                
                // Auto submit form if value changed significantly
                const originalValue = this.defaultValue;
                if (this.value !== originalValue) {
                    this.form.submit();
                }
            });
        });

        // Add confirmation for clear cart
        const clearCartForm = document.querySelector('form[action*="cart.clear"]');
        if (clearCartForm) {
            clearCartForm.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endsection