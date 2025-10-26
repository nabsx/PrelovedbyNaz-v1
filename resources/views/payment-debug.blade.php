@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Debug Pembayaran Midtrans</h1>
            <p class="text-gray-600">Halaman ini membantu Anda debug masalah pembayaran Midtrans</p>
        </div>

        <!-- Status Transaksi -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-pink-100">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Status Transaksi Terakhir</h2>
            
            @if($transaction)
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Kode Transaksi</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $transaction->transaction_code }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Status</p>
                            <p class="text-lg font-semibold">
                                @if($transaction->isPaid())
                                    <span class="text-green-600">✓ PAID</span>
                                @elseif($transaction->isPending())
                                    <span class="text-yellow-600">⏳ PENDING</span>
                                @else
                                    <span class="text-red-600">✗ EXPIRED</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Total Harga</p>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Dibayar Pada</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $transaction->paid_at ? $transaction->paid_at->format('d M Y H:i:s') : '-' }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    @if($transaction->payment_details)
                    <div class="mt-6 pt-6 border-t-2 border-pink-100">
                        <p class="text-gray-600 text-sm mb-3">Detail Pembayaran (JSON)</p>
                        <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-auto max-h-64">{{ json_encode($transaction->payment_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-gray-600">Tidak ada transaksi yang ditemukan</p>
            @endif
        </div>

        <!-- Test Notification -->
        @if($transaction && $transaction->isPending())
        <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-8 mb-8">
            <h2 class="text-2xl font-semibold text-blue-900 mb-4">Test Notifikasi Manual</h2>
            <p class="text-blue-900 mb-6">Klik tombol di bawah untuk mensimulasikan notifikasi pembayaran dari Midtrans. Ini akan mengubah status transaksi menjadi PAID.</p>
            
            <button 
                onclick="testNotification('{{ $transaction->transaction_code }}')"
                class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold"
            >
                <i class="fas fa-flask mr-2"></i>Test Notifikasi Pembayaran
            </button>
            
            <div id="test-result" class="mt-4 hidden p-4 rounded-lg"></div>
        </div>
        @endif

        <!-- Configuration Check -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8 border-2 border-pink-100">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Cek Konfigurasi</h2>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-900">Midtrans Server Key</span>
                    <span class="text-sm">
                        @if(config('midtrans.server_key'))
                            <span class="text-green-600 font-semibold">✓ Configured</span>
                        @else
                            <span class="text-red-600 font-semibold">✗ Not Configured</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-900">Midtrans Client Key</span>
                    <span class="text-sm">
                        @if(config('midtrans.client_key'))
                            <span class="text-green-600 font-semibold">✓ Configured</span>
                        @else
                            <span class="text-red-600 font-semibold">✗ Not Configured</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-900">Midtrans Merchant ID</span>
                    <span class="text-sm">
                        @if(config('midtrans.merchant_id'))
                            <span class="text-green-600 font-semibold">✓ Configured</span>
                        @else
                            <span class="text-red-600 font-semibold">✗ Not Configured</span>
                        @endif
                    </span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <span class="text-gray-900">Notification URL</span>
                    <span class="text-sm font-mono">{{ url('/payment/notification') }}</span>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-8">
            <h2 class="text-2xl font-semibold text-yellow-900 mb-4">Instruksi Setup</h2>
            <ol class="text-yellow-900 space-y-3 list-decimal list-inside">
                <li>Pastikan Midtrans credentials sudah diset di file <code class="bg-yellow-100 px-2 py-1 rounded">.env</code></li>
                <li>Di Midtrans Dashboard, set Notification URL ke: <code class="bg-yellow-100 px-2 py-1 rounded">{{ url('/payment/notification') }}</code></li>
                <li>Jika menggunakan localhost, gunakan ngrok: <code class="bg-yellow-100 px-2 py-1 rounded">ngrok http 8000</code></li>
                <li>Update Notification URL di Midtrans Dashboard dengan URL ngrok Anda</li>
                <li>Cek file <code class="bg-yellow-100 px-2 py-1 rounded">storage/logs/laravel.log</code> untuk melihat detail notifikasi</li>
            </ol>
        </div>
    </div>
</div>

<script>
function testNotification(transactionCode) {
    const resultDiv = document.getElementById('test-result');
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = '<p class="text-blue-900">Mengirim test notifikasi...</p>';
    
    fetch(`/transaction/test-notification/${transactionCode}`)
        .then(response => response.json())
        .then(data => {
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = `
                <div class="bg-green-100 border-2 border-green-300 p-4 rounded-lg">
                    <p class="text-green-900 font-semibold">✓ Test Notifikasi Berhasil!</p>
                    <p class="text-green-900 text-sm mt-2">Silakan refresh halaman untuk melihat status terbaru.</p>
                </div>
            `;
            setTimeout(() => location.reload(), 2000);
        })
        .catch(error => {
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = `
                <div class="bg-red-100 border-2 border-red-300 p-4 rounded-lg">
                    <p class="text-red-900 font-semibold">✗ Test Notifikasi Gagal</p>
                    <p class="text-red-900 text-sm mt-2">${error.message}</p>
                </div>
            `;
        });
}
</script>
@endsection
