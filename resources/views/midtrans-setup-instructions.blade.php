@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-pink-100">
            <h1 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                <i class="fas fa-cog text-pink-500"></i>
                Setup Midtrans Notification URL
            </h1>

            <div class="space-y-8">
                <!-- Step 1 -->
                <div class="border-l-4 border-pink-500 pl-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-pink-500 text-white rounded-full mr-3">1</span>
                        Buka Midtrans Dashboard
                    </h2>
                    <p class="text-gray-700 mb-3">Kunjungi dashboard Midtrans di:</p>
                    <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm break-all">
                        https://dashboard.sandbox.midtrans.com
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="border-l-4 border-pink-500 pl-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-pink-500 text-white rounded-full mr-3">2</span>
                        Pergi ke Settings → Configuration
                    </h2>
                    <p class="text-gray-700 mb-3">Navigasi ke menu Settings dan pilih Configuration</p>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <p class="text-yellow-900"><strong>Catatan:</strong> Pastikan Anda sudah login dengan akun Midtrans Anda</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="border-l-4 border-pink-500 pl-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-pink-500 text-white rounded-full mr-3">3</span>
                        Set HTTP Notification URL
                    </h2>
                    <p class="text-gray-700 mb-3">Cari field "HTTP Notification URL" dan isi dengan URL berikut:</p>
                    <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm break-all mb-3">
                        {{ url('/payment/notification') }}
                    </div>
                    <button onclick="copyNotificationUrl()" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                        <i class="fas fa-copy mr-2"></i>Salin URL
                    </button>
                </div>

                <!-- Step 4 -->
                <div class="border-l-4 border-pink-500 pl-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-pink-500 text-white rounded-full mr-3">4</span>
                        Simpan Perubahan
                    </h2>
                    <p class="text-gray-700">Klik tombol "Save" atau "Update" untuk menyimpan konfigurasi</p>
                </div>

                <!-- Important Note -->
                <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Informasi Penting
                    </h3>
                    <ul class="text-blue-900 space-y-2">
                        <li><strong>• Sandbox URL:</strong> Gunakan URL di atas untuk testing di sandbox</li>
                        <li><strong>• Production URL:</strong> Saat go live, ganti dengan domain production Anda</li>
                        <li><strong>• Signature Verification:</strong> Sistem akan otomatis verify signature dari Midtrans</li>
                        <li><strong>• Auto Update:</strong> Saat pembayaran berhasil, status di database akan terupdate otomatis</li>
                    </ul>
                </div>

                <!-- Verification -->
                <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Verifikasi Setup
                    </h3>
                    <p class="text-green-900 mb-4">Setelah setup, lakukan pembayaran test di halaman checkout. Jika berhasil:</p>
                    <ul class="text-green-900 space-y-2">
                        <li>✓ Status di database akan berubah menjadi "paid"</li>
                        <li>✓ Halaman success akan menampilkan "Pembayaran Berhasil"</li>
                        <li>✓ Tombol "Bayar Sekarang" akan hilang</li>
                    </ul>
                </div>

                <!-- Debug Info -->
                <div class="bg-gray-50 border-2 border-gray-200 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-bug text-gray-500"></i>
                        Debug Information
                    </h3>
                    <p class="text-gray-700 mb-3">Jika ada masalah, cek file log di:</p>
                    <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm break-all">
                        storage/logs/laravel.log
                    </div>
                    <p class="text-gray-600 text-sm mt-3">Cari log dengan keyword "Midtrans Notification" untuk melihat detail notifikasi yang diterima</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 mt-8">
                <a href="{{ route('checkout.form') }}" class="flex-1 px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all font-semibold shadow-lg text-center">
                    Kembali ke Checkout
                </a>
                <a href="{{ route('midtrans.test.page') }}" class="flex-1 px-6 py-3 border-2 border-pink-300 text-pink-600 rounded-xl hover:bg-pink-50 transition-colors text-center font-semibold">
                    Test Credentials
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyNotificationUrl() {
    const url = '{{ url("/payment/notification") }}';
    navigator.clipboard.writeText(url).then(() => {
        alert('Notification URL berhasil disalin!');
    }).catch(() => {
        alert('Gagal menyalin URL');
    });
}
</script>
@endsection
