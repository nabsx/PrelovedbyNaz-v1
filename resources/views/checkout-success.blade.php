@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 @if($transaction->isPaid()) bg-green-100 @else bg-blue-100 @endif rounded-full mb-4">
                <i class="fas @if($transaction->isPaid()) fa-check text-3xl text-green-600 @else fa-clock text-3xl text-blue-600 @endif"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                @if($transaction->isPaid())
                    Pembayaran Berhasil!
                @else
                    Pesanan Berhasil Dibuat!
                @endif
            </h1>
            <p class="text-gray-600">
                @if($transaction->isPaid())
                    Terima kasih telah melakukan pembayaran. Pesanan Anda sedang diproses.
                @else
                    Terima kasih telah berbelanja. Silakan lakukan pembayaran sesuai metode yang dipilih.
                @endif
            </p>
        </div>

        <!-- Transaction Code Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-pink-100">
            <div class="text-center mb-6">
                <p class="text-gray-600 text-sm mb-2">KODE TRANSAKSI</p>
                <div class="bg-gradient-to-r from-pink-50 to-pink-100 p-6 rounded-xl border-2 border-pink-300">
                    <p class="text-3xl font-bold text-pink-600 font-mono tracking-wider">
                        {{ $transaction->transaction_code }}
                    </p>
                </div>
                <p class="text-gray-600 text-sm mt-3">Simpan kode ini untuk melacak pesanan Anda</p>
            </div>

            <!-- Copy Button -->
            <div class="flex justify-center mb-6">
                <button 
                    onclick="copyToClipboard('{{ $transaction->transaction_code }}')"
                    class="px-6 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors flex items-center gap-2"
                >
                    <i class="fas fa-copy"></i>
                    Salin Kode
                </button>
            </div>

            <!-- Add payment status badge -->
            <div class="text-center">
                <span id="status-badge" class="inline-block px-4 py-2 @if($transaction->isPaid()) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif rounded-full text-sm font-semibold">
                    <i class="fas @if($transaction->isPaid()) fa-check-circle @else fa-hourglass-half @endif mr-2"></i>
                    <span id="status-text">@if($transaction->isPaid())Sudah Dibayar@else Menunggu Pembayaran @endif</span>
                </span>
            </div>
        </div>

        <!-- Midtrans Payment Widget - only show if not paid -->
        @if($transaction->snap_token && $transaction->isPending())
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-pink-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-credit-card text-pink-500"></i>
                Lakukan Pembayaran
            </h2>
            <button 
                id="pay-button"
                class="w-full px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all font-semibold shadow-lg"
            >
                Bayar Sekarang - Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
            </button>
        </div>
        @elseif($transaction->isPaid())
        <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-8 mb-8">
            <h2 class="text-xl font-semibold text-green-900 mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i>
                Pembayaran Berhasil
            </h2>
            <p class="text-green-900">Pesanan Anda telah dikonfirmasi dan sedang diproses untuk pengiriman.</p>
        </div>
        @endif

        <!-- Order Details -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-pink-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-receipt text-pink-500"></i>
                Detail Pesanan
            </h2>

            <!-- Order Items -->
            <div class="space-y-4 mb-6 pb-6 border-b-2 border-pink-100">
                @foreach($transaction->items as $item)
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <p class="font-medium text-gray-900">
                            Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Totals -->
            <div class="space-y-3">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Pengiriman</span>
                    <span class="text-green-600 font-semibold">Gratis</span>
                </div>
                <div class="flex justify-between text-lg font-bold bg-gradient-to-r from-pink-50 to-pink-100 p-4 rounded-lg border-2 border-pink-200">
                    <span>Total</span>
                    <span class="bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
                        Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-pink-100">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-pink-500"></i>
                Alamat Pengiriman
            </h2>
            <div class="text-gray-700 space-y-2">
                <p>{{ $transaction->address }}</p>
                <p>{{ $transaction->city }}, {{ $transaction->postal_code }}</p>
                <p class="text-gray-600">Telepon: {{ $transaction->phone }}</p>
            </div>
        </div>

        <!-- Payment Instructions -->
        @if($transaction->isPending())
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-8 mb-8">
            <h2 class="text-xl font-semibold text-blue-900 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                Instruksi Pembayaran
            </h2>
            <div class="text-blue-900 space-y-3">
                <p>Metode Pembayaran: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span></p>
                <p>Silakan lakukan pembayaran sebesar <span class="font-bold">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span> ke metode pembayaran yang telah dipilih.</p>
                <p class="text-sm">Pembayaran harus dilakukan dalam waktu 24 jam. Jika tidak, pesanan akan dibatalkan secara otomatis.</p>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-4">
            <a href="{{ route('products.index') }}" class="flex-1 px-6 py-3 border-2 border-pink-300 text-pink-600 rounded-xl hover:bg-pink-50 transition-colors text-center font-semibold">
                Lanjut Belanja
            </a>
            <a href="{{ route('transactions.history') }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all font-semibold shadow-lg">
                Lihat Pesanan Saya
            </a>
        </div>
    </div>
</div>

<!-- Midtrans Snap Script -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Kode transaksi berhasil disalin!');
    }).catch(() => {
        alert('Gagal menyalin kode transaksi');
    });
}

let pollCount = 0;
const maxPolls = 240; // 4 minutes with 1 second interval
let isPolling = false;
let pollTimeout;

function checkPaymentStatus() {
    if (isPolling) return;
    isPolling = true;
    
    const transactionCode = '{{ $transaction->transaction_code }}';
    
    fetch('{{ route("transaction.check-status", $transaction->transaction_code) }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('[v0] Status response:', data);
        isPolling = false;
        
        if (data.is_paid) {
            console.log('[v0] Payment confirmed! Updating UI...');
            updateUIToPaid();
            clearTimeout(pollTimeout);
            // Reload page after 2 seconds to show full updated state
            setTimeout(() => location.reload(), 2000);
        } else if (pollCount < maxPolls) {
            pollCount++;
            pollTimeout = setTimeout(checkPaymentStatus, 1000);
        } else {
            console.log('[v0] Max polls reached, stopping polling');
            isPolling = false;
        }
    })
    .catch(error => {
        console.error('[v0] Error checking status:', error);
        isPolling = false;
        if (pollCount < maxPolls) {
            pollCount++;
            pollTimeout = setTimeout(checkPaymentStatus, 2000); // Retry after 2 seconds on error
        }
    });
}

function updateUIToPaid() {
    const badge = document.getElementById('status-badge');
    const statusText = document.getElementById('status-text');
    
    if (badge) {
        badge.className = 'inline-block px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold';
        badge.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Sudah Dibayar';
    }
    
    // Hide payment button if exists
    const payButton = document.getElementById('pay-button');
    if (payButton) {
        payButton.closest('.bg-white').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('[v0] Page loaded, starting payment status check...');
    
    // Only start polling if transaction is still pending
    @if($transaction->isPending())
        checkPaymentStatus();
    @endif
});

document.getElementById('pay-button')?.addEventListener('click', function() {
    console.log('[v0] Pay button clicked');
    snap.pay('{{ $transaction->snap_token }}', {
        onSuccess: function(result) {
            console.log('[v0] Payment success callback triggered');
            pollCount = 0;
            checkPaymentStatus();
        },
        onPending: function(result) {
            console.log('[v0] Payment pending callback triggered');
            pollCount = 0;
            checkPaymentStatus();
        },
        onError: function(result) {
            console.log('[v0] Payment error callback triggered');
            alert('Pembayaran gagal. Silakan coba lagi.');
        },
        onClose: function() {
            console.log('[v0] Payment popup closed, checking status...');
            pollCount = 0;
            checkPaymentStatus();
        }
    });
});
</script>
@endsection
