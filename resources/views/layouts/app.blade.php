<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrelovedByNaz - Preloved Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pink: {
                            50: '#fff5f8',
                            100: '#ffe4f0',
                            200: '#ffc9e3',
                            300: '#ffadd6',
                            400: '#ff92c9',
                            500: '#ff69b4',
                            600: '#ff1493',
                            700: '#e60b7f',
                            800: '#cc0a6f',
                            900: '#b3085f',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #fff5f8 0%, #ffe4f0 100%);
        }

        .dropdown-content {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .dropdown:hover .dropdown-content,
        .dropdown:focus-within .dropdown-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .btn-pink {
            background: linear-gradient(135deg, #ff69b4 0%, #ff1493 100%);
            transition: all 0.3s ease;
        }

        .btn-pink:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 20, 147, 0.3);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(255, 20, 147, 0.2);
        }

        .search-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #ffe4f0;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #ff69b4;
            box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.1);
        }

        @media (max-width: 768px) {
            .dropdown-content {
                position: static;
                opacity: 1;
                visibility: visible;
                transform: none;
                display: none;
            }
            
            .dropdown.active .dropdown-content {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ asset('images/logo.png') }}" alt="Preloved Logo" class="h-16 w-auto">
                        @else
                            <div class="text-2xl font-bold bg-gradient-to-r from-pink-500 to-pink-600 bg-clip-text text-transparent">
                                <i class="fas fa-crown mr-2"></i>
                            </div>
                        @endif
                    </a>
                </div>

                <!-- Menu Items -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Produk</a>
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Keranjang</a>
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Check Pesanan</a>
                </div>

                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->
                    <form action="{{ route('products.index') }}" method="GET" class="hidden md:flex">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Cari..." 
                                   value="{{ request('search') }}"
                                   class="search-input px-4 py-2 w-48 text-sm">
                            <button type="submit" class="absolute right-3 top-2.5 text-pink-500">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-pink-600 relative transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        @auth
                            @php
                                $cartCount = \App\Models\CartItem::active()
                                    ->forUser(auth()->user())
                                    ->count();
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-pink-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center font-bold">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        @endauth
                    </a>

                    <!-- User Menu -->
                    @auth
                        <div class="dropdown relative" id="userDropdown">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-pink-600 focus:outline-none py-2 px-3 rounded-lg hover:bg-pink-50 transition-colors">
                                <span class="font-medium">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="dropdownArrow"></i>
                            </button>
                            <div class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50 border border-pink-100">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('transactions.history') }}" class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
                                    <i class="fas fa-history mr-2"></i>Riwayat Transaksi
                                </a>
                                <div class="border-t border-pink-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex space-x-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-pink-600 font-medium transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 btn-pink text-white rounded-full font-medium">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-pink-500 to-pink-600 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Preloved Logo" class="h-24 w-auto mb-2">
                    @else
                        <h3 class="text-2xl font-bold mb-2">
                            <i class="fas fa-crown mr-2"></i>Preloved by Naz
                        </h3>
                    @endif
                    <p class="text-pink-100 text-sm">Platform terpercaya untuk membeli barang preloved dengan harga terbaik dan aman.</p>
                </div>

                <!-- Informasi -->
                <div>
                    <h4 class="font-semibold mb-4">Informasi</h4>
                    <ul class="space-y-2 text-sm text-pink-100">
                        <li><a href="#" class="hover:text-white transition-colors">FAQ (Pertanyaan Umum)</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <!-- Hubungi Kami -->
                <div>
                    <h4 class="font-semibold mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm text-pink-100">
                        <li><a href="#" class="hover:text-white transition-colors">Instagram (@prelovedbynaz)</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-pink-400 pt-8">
                <p class="text-center text-pink-100 text-sm">
                Di Preloved by Naz, kami percaya bahwa setiap barang preloved memiliki cerita. Platform ini memudahkan komunitas untuk saling berbagi, menemukan, dan menikmati barang berkualitas tanpa harus khawatir soal keaslian, dengan harga yang bersahabat dan layanan yang terpercaya.
                </p>
                <p class="text-center text-pink-100 text-xs mt-4">Hak Cipta © 2025 PrelovedByNaz. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('userDropdown');
            if (!dropdown) return;

            const dropdownButton = dropdown.querySelector('button');
            const dropdownContent = dropdown.querySelector('.dropdown-content');
            const dropdownArrow = document.getElementById('dropdownArrow');

            dropdownButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = dropdownContent.style.opacity === '1';
                
                if (isOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });

            dropdown.addEventListener('mouseenter', function() {
                if (window.innerWidth > 768) {
                    openDropdown();
                }
            });

            dropdown.addEventListener('mouseleave', function(e) {
                if (window.innerWidth > 768) {
                    setTimeout(() => {
                        if (!dropdown.matches(':hover')) {
                            closeDropdown();
                        }
                    }, 100);
                }
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    closeDropdown();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDropdown();
                }
            });

            function openDropdown() {
                dropdownContent.style.opacity = '1';
                dropdownContent.style.visibility = 'visible';
                dropdownContent.style.transform = 'translateY(0)';
                dropdownArrow.style.transform = 'rotate(180deg)';
                dropdown.classList.add('active');
            }

            function closeDropdown() {
                dropdownContent.style.opacity = '0';
                dropdownContent.style.visibility = 'hidden';
                dropdownContent.style.transform = 'translateY(-10px)';
                dropdownArrow.style.transform = 'rotate(0deg)';
                dropdown.classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
