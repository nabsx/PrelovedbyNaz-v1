@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Updated header with gradient styling -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
            Pembayaran
        </h1>
        <p class="text-gray-600 mt-2">Selesaikan pembayaran untuk pesanan Anda</p>
    </div>

    <!-- Updated card styling with pink theme -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-pink-100">
        <div class="p-6">
            <!-- Order Details -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-receipt text-pink-500"></i>
                    Detail Pesanan
                </h2>
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-xl p-4 border-2 border-pink-200">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600 font-medium">Order ID:</span>
                        <span class="font-mono text-gray-900 bg-white px-3 py-1 rounded-lg">{{ $transaction->midtrans_order_id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Total Pembayaran:</span>
                        <span class="text-3xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
                            Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-credit-card text-pink-500"></i>
                    Metode Pembayaran
                </h2>
                <p class="text-gray-600 mb-4">Pilih metode pembayaran yang tersedia:</p>
                
                <!-- Midtrans Payment Gateway -->
                <div id="snap-container" class="bg-pink-50 rounded-xl p-4 border-2 border-pink-200"></div>
            </div>

            <!-- Expiration Warning -->
            <!-- Updated warning styling -->
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
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
