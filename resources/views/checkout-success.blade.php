@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Success Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <i class="fas fa-check text-3xl text-green-600"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Pesanan Berhasil Dibuat!</h1>
            <p class="text-gray-600">Terima kasih telah berbelanja. Silakan lakukan pembayaran sesuai metode yang dipilih.</p>
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
        </div>

        <!-- Midtrans Payment Widget -->
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

document.getElementById('pay-button')?.addEventListener('click', function() {
    snap.pay('{{ $transaction->snap_token }}', {
        onSuccess: function(result) {
            console.log('Payment success:', result);
            // Redirect ke halaman sukses atau refresh
            location.reload();
        },
        onPending: function(result) {
            console.log('Payment pending:', result);
        },
        onError: function(result) {
            console.log('Payment error:', result);
            alert('Pembayaran gagal. Silakan coba lagi.');
        },
        onClose: function() {
            console.log('Payment popup closed');
        }
    });
});
</script>
@endsection
