@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="text-gray-600 mt-2">Selesaikan pembayaran untuk pesanan Anda</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Detail Pesanan</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-mono text-gray-900">{{ $transaction->midtrans_order_id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Pembayaran:</span>
                        <span class="text-2xl font-bold text-pink-600">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Metode Pembayaran</h2>
                <p class="text-gray-600 mb-4">Pilih metode pembayaran yang tersedia:</p>
                
                <!-- Midtrans Payment Gateway -->
                <div id="snap-container"></div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-500 mr-3"></i>
                    <div>
                        <p class="text-yellow-700 text-sm font-semibold">Batas Waktu Pembayaran</p>
                        <p class="text-yellow-600 text-sm">
                            Selesaikan pembayaran dalam 
                            <span class="font-bold">{{ $transaction->expires_at->diffForHumans() }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap JS -->
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Embed Snap payment form
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                // Redirect to success page
                window.location.href = '{{ route('transactions.history') }}?status=success';
            },
            onPending: function(result) {
                // Redirect to pending page
                window.location.href = '{{ route('transactions.history') }}?status=pending';
            },
            onError: function(result) {
                // Redirect to error page
                window.location.href = '{{ route('transactions.history') }}?status=error';
            },
            onClose: function() {
                // User closed the payment form
                alert('Anda menutup halaman pembayaran. Pesanan masih menunggu pembayaran.');
            }
        });
    });
</script>
@endsection