@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="mt-2 text-gray-600">Lengkapi pesanan Anda dengan mengisi detail di bawah</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <form action="{{ route('checkout') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf

                    <!-- Shipping Address Section -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Alamat Pengiriman</h2>
                        
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Lengkap
                            </label>
                            <textarea 
                                id="address" 
                                name="address" 
                                rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('address') border-red-500 @enderror"
                                placeholder="Masukkan alamat lengkap Anda"
                                required
                            >{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kota
                                </label>
                                <input 
                                    type="text" 
                                    id="city" 
                                    name="city"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('city') border-red-500 @enderror"
                                    placeholder="Masukkan kota Anda"
                                    value="{{ old('city') }}"
                                    required
                                >
                                @error('city')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kode Pos
                                </label>
                                <input 
                                    type="text" 
                                    id="postal_code" 
                                    name="postal_code"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('postal_code') border-red-500 @enderror"
                                    placeholder="Masukkan kode pos"
                                    value="{{ old('postal_code') }}"
                                    required
                                >
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                placeholder="Masukkan nomor telepon Anda"
                                value="{{ old('phone') }}"
                                required
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method Section -->
                    <div class="mb-8 pb-8 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-pink-500 mb-6">Pilih Metode Pembayaran</h2>
                        
                        <div class="space-y-3">
                            @foreach($paymentMethods as $key => $method)
                                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-pink-50 transition @if(old('payment_method') == $key) bg-pink-50 border-pink-500 @endif">
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="{{ $key }}"
                                        class="w-4 h-4 text-pink-500"
                                        @if(old('payment_method') == $key || $loop->first) checked @endif
                                    >
                                    <div class="ml-4 flex-1">
                                        <p class="font-semibold text-gray-900">{{ $method['name'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $method['description'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        <a href="{{ route('cart.index') }}" class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 text-center">
                            Kembali ke Keranjang
                        </a>
                        <button type="submit" class="flex-1 px-6 py-3 bg-pink-500 text-white rounded-lg font-medium hover:bg-pink-600 transition">
                            Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between items-start pb-4 border-b border-gray-200">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-medium text-gray-900">
                                    Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Pengiriman</span>
                            <span class="text-gray-900">Gratis</span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold text-gray-900 pt-4 border-t border-gray-200">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
