<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(236, 72, 153, 0.3); }
            50% { box-shadow: 0 0 40px rgba(236, 72, 153, 0.6); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-white to-pink-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Left Side - Illustration -->
                <div class="flex justify-center">
                    <div class="relative">
                        <div class="float-animation">
                            <div class="w-48 h-48 bg-gradient-to-br from-pink-200 to-pink-300 rounded-full flex items-center justify-center pulse-glow">
                                <div class="text-center">
                                    <i class="fas fa-hourglass-end text-pink-600 text-6xl mb-4 block"></i>
                                    <p class="text-pink-700 font-bold text-sm">Session Expired</p>
                                </div>
                            </div>
                        </div>
                        <!-- Decorative circles -->
                        <div class="absolute top-0 right-0 w-20 h-20 bg-pink-200 rounded-full opacity-50"></div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-pink-300 rounded-full opacity-30"></div>
                    </div>
                </div>

                <!-- Right Side - Content -->
                <div class="text-center md:text-left">
                    <h1 class="text-7xl md:text-8xl font-black bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent mb-4">
                        419
                    </h1>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Sesi Berakhir
                    </h2>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali untuk melanjutkan berbelanja.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-full hover:shadow-lg hover:shadow-pink-300 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login Kembali
                        </a>
                        
                        <a href="{{ url('/') }}" 
                           class="inline-flex items-center justify-center px-8 py-3 border-2 border-pink-500 text-pink-600 font-semibold rounded-full hover:bg-pink-50 transition-all duration-300">
                            <i class="fas fa-home mr-2"></i>
                            Kembali ke Beranda
                        </a>
                    </div>

                    <!-- Support Info -->
                    <div class="mt-8 pt-8 border-t border-pink-200">
                        <p class="text-gray-600 text-sm mb-4">Butuh bantuan?</p>
                        <div class="flex gap-4 justify-center md:justify-start">
                            <a href="https://t.me/mayobox" target="_blank" class="text-pink-600 hover:text-pink-700 transition-colors">
                                <i class="fab fa-telegram text-2xl"></i>
                            </a>
                            <a href="https://instagram.com/mayobox" target="_blank" class="text-pink-600 hover:text-pink-700 transition-colors">
                                <i class="fab fa-instagram text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
