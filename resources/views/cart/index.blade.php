@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
            Keranjang Belanja
        </h1>
        <p class="text-gray-600 mt-2">Review produk yang akan Anda beli</p>
    </div>

    @if($cartItems->count() > 0)
        <div id="messageContainer" class="mb-6"></div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-pink-100">
            <div class="p-6">
                <div class="space-y-6">
                    @foreach($cartItems as $cartItem)
                    <div class="flex items-center space-x-4 border-b border-pink-100 pb-6 last:border-b-0 hover:bg-pink-50 p-4 rounded-xl transition" data-cart-item-id="{{ $cartItem->id }}">
                        <div class="flex-shrink-0">
                            <img src="{{ $cartItem->product->image_url }}" 
                                 alt="{{ $cartItem->product->name }}"
                                 class="w-20 h-20 object-cover rounded-xl border-2 border-pink-200">
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $cartItem->product->name }}
                            </h3>
                            <p class="text-gray-600 text-sm mt-1">
                                {{ Str::limit($cartItem->product->description, 50) }}
                            </p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="text-2xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
                                    Rp {{ number_format($cartItem->product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-sm text-gray-500 bg-pink-100 px-3 py-1 rounded-full">
                                    Stok: {{ $cartItem->product->stock }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2 bg-pink-50 rounded-xl p-2">
                                <button type="button" 
                                        class="quantity-decrease w-8 h-8 flex items-center justify-center text-pink-600 hover:bg-pink-200 rounded-lg transition-colors"
                                        data-cart-item-id="{{ $cartItem->id }}"
                                        data-current-quantity="{{ $cartItem->quantity }}"
                                        data-max-quantity="{{ $cartItem->product->stock }}">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                
                                <input type="number" 
                                       class="quantity-input w-12 text-center border-0 bg-white rounded-lg py-1 font-semibold"
                                       value="{{ $cartItem->quantity }}" 
                                       min="1" 
                                       max="{{ $cartItem->product->stock }}"
                                       data-cart-item-id="{{ $cartItem->id }}"
                                       data-max-quantity="{{ $cartItem->product->stock }}">
                                
                                <button type="button" 
                                        class="quantity-increase w-8 h-8 flex items-center justify-center text-pink-600 hover:bg-pink-200 rounded-lg transition-colors"
                                        data-cart-item-id="{{ $cartItem->id }}"
                                        data-current-quantity="{{ $cartItem->quantity }}"
                                        data-max-quantity="{{ $cartItem->product->stock }}">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>

                            <form action="{{ route('cart.destroy', $cartItem) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t-2 border-pink-100">
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-2xl font-bold text-gray-900">Total:</span>
                        <span class="text-3xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent" id="cart-total">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex justify-between space-x-4">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-3 border-2 border-pink-300 text-pink-600 rounded-xl hover:bg-pink-50 transition-colors font-semibold">
                                <i class="fas fa-trash mr-2"></i>Kosongkan Keranjang
                            </button>
                        </form>

                        <a href="{{ route('checkout.form') }}" 
                           class="px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all font-semibold inline-flex items-center shadow-lg transform hover:scale-105">
                            <i class="fas fa-credit-card mr-2"></i>Checkout Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                <p class="text-yellow-700 text-sm">
                    <strong>Perhatian:</strong> Item di keranjang akan otomatis dihapus setelah <span id="expiration-timer" class="font-bold">20</span> menit tidak ada aktivitas.
                </p>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-gradient-to-br from-pink-50 to-white rounded-2xl border-2 border-pink-100">
            <div class="mx-auto w-24 h-24 bg-gradient-to-br from-pink-200 to-pink-300 rounded-full flex items-center justify-center mb-4 shadow-lg">
                <i class="fas fa-shopping-cart text-white text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
            <p class="text-gray-600 mb-6">Belum ada produk yang ditambahkan ke keranjang</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all shadow-lg transform hover:scale-105">
                <i class="fas fa-shopping-bag mr-2"></i>
                Mulai Belanja
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const decreaseButtons = document.querySelectorAll('.quantity-decrease');
        const increaseButtons = document.querySelectorAll('.quantity-increase');

        function showMessage(message, type = 'error') {
            const container = document.getElementById('messageContainer');
            const bgColor = type === 'success' ? 'bg-green-50' : 'bg-red-50';
            const borderColor = type === 'success' ? 'border-green-200' : 'border-red-200';
            const textColor = type === 'success' ? 'text-green-700' : 'text-red-700';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            container.innerHTML = `
                <div class="${bgColor} border ${borderColor} ${textColor} px-4 py-3 rounded-lg flex items-center gap-2">
                    <i class="fas ${icon}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        // Function to update quantity via AJAX
        function updateQuantityAjax(cartItemId, newQuantity) {
            const inputElement = document.querySelector(`.quantity-input[data-cart-item-id="${cartItemId}"]`);
            const maxQuantity = parseInt(inputElement?.dataset.maxQuantity) || 0;
            
            if (newQuantity < 1 || newQuantity > maxQuantity) {
                console.log("[v0] Invalid quantity:", newQuantity, "Max:", maxQuantity);
                return;
            }

            fetch(`{{ route('cart.updateQuantityAjax', ':cartItemId') }}`.replace(':cartItemId', cartItemId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                console.log("[v0] Response:", data);
                if (data.success) {
                    // Update input value
                    if (inputElement) {
                        inputElement.value = newQuantity;
                    }
                    
                    // Update cart total
                    const cartTotal = document.getElementById('cart-total');
                    if (cartTotal && data.formattedTotal) {
                        cartTotal.textContent = data.formattedTotal;
                    }
                    
                    showMessage('Keranjang berhasil diperbarui', 'success');
                } else {
                    showMessage(data.message || 'Gagal mengupdate keranjang', 'error');
                    // Reset input ke nilai sebelumnya
                    if (inputElement) {
                        location.reload();
                    }
                }
            })
            .catch(error => {
                console.error('[v0] Error:', error);
                showMessage('Terjadi kesalahan saat mengupdate keranjang', 'error');
            });
        }

        // Quantity input change event
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const cartItemId = this.dataset.cartItemId;
                const newQuantity = parseInt(this.value);
                const maxQuantity = parseInt(this.dataset.maxQuantity);

                if (newQuantity < 1) {
                    this.value = 1;
                    updateQuantityAjax(cartItemId, 1);
                } else if (newQuantity > maxQuantity) {
                    this.value = maxQuantity;
                    showMessage(`Stok tidak mencukupi. Maksimal: ${maxQuantity}`, 'error');
                    updateQuantityAjax(cartItemId, maxQuantity);
                } else {
                    updateQuantityAjax(cartItemId, newQuantity);
                }
            });
        });

        // Decrease button click
        decreaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartItemId = this.dataset.cartItemId;
                const input = document.querySelector(`.quantity-input[data-cart-item-id="${cartItemId}"]`);
                const currentQuantity = parseInt(input?.value) || 1;
                
                if (currentQuantity > 1) {
                    updateQuantityAjax(cartItemId, currentQuantity - 1);
                }
            });
        });

        // Increase button click
        increaseButtons.forEach(button => {
            button.addEventListener('click', function() {
                const cartItemId = this.dataset.cartItemId;
                const input = document.querySelector(`.quantity-input[data-cart-item-id="${cartItemId}"]`);
                const currentQuantity = parseInt(input?.value) || 1;
                const maxQuantity = parseInt(input?.dataset.maxQuantity) || 0;
                
                if (currentQuantity < maxQuantity) {
                    updateQuantityAjax(cartItemId, currentQuantity + 1);
                } else {
                    showMessage(`Stok tidak mencukupi. Maksimal: ${maxQuantity}`, 'error');
                }
            });
        });

        // Delete form confirmation
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
                    e.preventDefault();
                }
            });
        });

        // Clear cart confirmation
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
