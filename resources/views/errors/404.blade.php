@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full">
        <!-- Main Container -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-pink-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 md:p-12">
                <!-- Left Side - Illustration -->
                <div class="flex items-center justify-center">
                    <div class="relative w-full h-64 md:h-80">
                        <!-- Animated 404 Text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <!-- Floating Animation -->
                                <style>
                                    @keyframes float {
                                        0%, 100% { transform: translateY(0px); }
                                        50% { transform: translateY(-20px); }
                                    }
                                    @keyframes spin {
                                        0% { transform: rotate(0deg); }
                                        100% { transform: rotate(360deg); }
                                    }
                                    .float-animation {
                                        animation: float 3s ease-in-out infinite;
                                    }
                                    .spin-animation {
                                        animation: spin 20s linear infinite;
                                    }
                                </style>
                                
                                <!-- Decorative Circle -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-48 h-48 bg-gradient-to-br from-pink-100 to-pink-50 rounded-full opacity-50 spin-animation"></div>
                                </div>

                                <!-- 404 Number -->
                                <div class="relative z-10 float-animation">
                                    <h1 class="text-8xl md:text-9xl font-black bg-gradient-to-r from-pink-500 via-pink-600 to-pink-700 bg-clip-text text-transparent">
                                        404
                                    </h1>
                                </div>

                                <!-- Decorative Elements -->
                                <div class="absolute top-0 right-0 w-12 h-12 bg-pink-300 rounded-full opacity-60"></div>
                                <div class="absolute bottom-0 left-0 w-8 h-8 bg-pink-200 rounded-full opacity-60"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Content -->
                <div class="flex flex-col justify-center">
                    <!-- Added gradient text for heading -->
                    <h2 class="text-4xl md:text-5xl font-black mb-4 bg-gradient-to-r from-pink-600 to-pink-700 bg-clip-text text-transparent">
                        Oops!
                    </h2>

                    <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-4">
                        Halaman Tidak Ditemukan
                    </h3>

                    <p class="text-gray-600 text-base md:text-lg mb-8 leading-relaxed">
                        Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan. Jangan khawatir, kami siap membantu Anda menemukan apa yang dicari!
                    </p>

                    <!-- Helpful Links -->
                    <div class="mb-8">
                        <p class="text-sm font-semibold text-gray-700 mb-4">Apa yang ingin Anda lakukan?</p>
                        <div class="space-y-3">
                            <a href="{{ route('home') }}" 
                               class="flex items-center px-4 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl hover:shadow-lg hover:shadow-pink-300 transition-all duration-300 transform hover:scale-105 font-medium">
                                <i class="fas fa-home mr-3"></i>
                                Kembali ke Beranda
                            </a>
                            <a href="{{ route('products.index') }}" 
                               class="flex items-center px-4 py-3 bg-pink-100 text-pink-600 rounded-xl hover:bg-pink-200 transition-all duration-300 font-medium">
                                <i class="fas fa-shopping-bag mr-3"></i>
                                Lihat Produk
                            </a>
                        </div>
                    </div>

                    <!-- Contact Support -->
                    <div class="pt-6 border-t border-pink-100">
                        <p class="text-sm text-gray-600 mb-3">
                            <i class="fas fa-headset text-pink-500 mr-2"></i>
                            Butuh bantuan? Hubungi kami di Telegram atau Instagram
                        </p>
                        <div class="flex space-x-3">
                            <a href="#" class="inline-flex items-center justify-center w-10 h-10 bg-pink-100 text-pink-600 rounded-full hover:bg-pink-200 transition-colors">
                                <i class="fab fa-telegram text-lg"></i>
                            </a>
                            <a href="#" class="inline-flex items-center justify-center w-10 h-10 bg-pink-100 text-pink-600 rounded-full hover:bg-pink-200 transition-colors">
                                <i class="fab fa-instagram text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Message -->
        <div class="text-center mt-8">
            <p class="text-gray-500 text-sm">
                Kode Error: <span class="font-mono font-bold text-pink-600">404 NOT FOUND</span>
            </p>
        </div>
    </div>
</div>
@endsection
