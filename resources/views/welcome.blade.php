@extends('layouts.app')

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="space-y-6">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                    Mengalami kesulitan saat membeli <span class="bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">Robux?</span>
                </h1>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Bergabunglah dengan komunitas kami di Discord atau kirim pesan langsung di Telegram untuk bantuan. Temukan Panduan Lengkapnya Melalui Tombol ini.
                </p>
                
                <!-- Social Links -->
                <div class="flex gap-4 pt-4">
                    <a href="#" class="w-12 h-12 rounded-full bg-pink-100 hover:bg-pink-500 text-pink-600 hover:text-white flex items-center justify-center transition-all duration-300">
                        <i class="fab fa-discord text-lg"></i>
                    </a>
                    <a href="#" class="w-12 h-12 rounded-full bg-pink-100 hover:bg-pink-500 text-pink-600 hover:text-white flex items-center justify-center transition-all duration-300">
                        <i class="fab fa-telegram text-lg"></i>
                    </a>
                    <a href="#" class="w-12 h-12 rounded-full bg-pink-100 hover:bg-pink-500 text-pink-600 hover:text-white flex items-center justify-center transition-all duration-300">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                </div>

                <!-- CTA Button -->
                <div class="pt-4">
                    <a href="{{ route('products.index') }}" class="inline-block btn-pink text-white px-8 py-3 rounded-full font-semibold text-lg">
                        Panduan
                    </a>
                </div>
            </div>

            <!-- Right Image -->
            <div class="relative">
                <div class="bg-gradient-to-br from-pink-100 to-pink-50 rounded-3xl p-8 border-2 border-pink-200">
                    <img src="/placeholder.svg?height=400&width=400" alt="Robux Guide" class="w-full rounded-2xl">
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
                Pilih Tipe Robux yang Ingin Kamu Beli
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Robux Gamepass PO -->
                <div class="card-hover bg-gradient-to-br from-pink-50 to-white rounded-2xl p-8 border-2 border-pink-200 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Robux Gamepass PO</h3>
                        <span class="text-3xl">👑</span>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Pesan Robux dengan harga termurah! Proses pengiriman estimasi 8-10 hari. Ideal untuk Kamu yang tidak terburu-buru dan mencari harga paling ekonomis.
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="text-4xl">💎</div>
                        <a href="{{ route('products.index') }}" class="btn-pink text-white px-6 py-2 rounded-full font-semibold">
                            Beli Sekarang
                        </a>
                        <div class="text-4xl">👧</div>
                    </div>
                </div>

                <!-- Robux Via Login -->
                <div class="card-hover bg-gradient-to-br from-pink-50 to-white rounded-2xl p-8 border-2 border-pink-200 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Robux Via Login</h3>
                        <span class="text-3xl">⚡</span>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Beli Robux jadi makin gampang, cukup pakai username dan password. Gak pakai ribet, gak pakai lama!
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="text-4xl">💎</div>
                        <a href="{{ route('products.index') }}" class="btn-pink text-white px-6 py-2 rounded-full font-semibold">
                            Beli Sekarang
                        </a>
                        <div class="text-4xl">👧</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Robux Gift Gamecard Section -->
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="bg-gradient-to-r from-pink-100 to-pink-50 rounded-3xl p-12 border-2 border-pink-200">
            <h3 class="text-3xl font-bold text-gray-900 mb-4">Robux Gift Gamecard</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">
                Dapatkan kode Robux Gift Gamecard yang bisa Anda redeem sendiri. Fleksibel untuk digunakan kapan saja!
            </p>
            <a href="{{ route('products.index') }}" class="inline-block btn-pink text-white px-8 py-3 rounded-full font-semibold">
                Lihat Penawaran
            </a>
        </div>
    </div>
</div>
@endsection
