<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prelovedbynaz - Marketplace Preloved Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pink: {
                            50: '#fdf2f8',
                            100: '#fce7f3',
                            500: '#ec4899',
                            600: '#db2777',
                            700: '#be185d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
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
        
        /* Untuk mobile */
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
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-pink-600">
                        <i class="fas fa-heart mr-2"></i>Prelovedbynaz
                    </a>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-pink-600">Produk</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-pink-600 relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        @auth
                            @php
                                $cartCount = \App\Models\CartItem::active()
                                    ->forUser(auth()->user())
                                    ->count();
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-2 bg-pink-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        @endauth
                    </a>

                    @auth
                        <div class="dropdown relative" id="userDropdown">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-pink-600 focus:outline-none focus:text-pink-600 py-2 px-3 rounded-lg hover:bg-pink-50 transition-colors">
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="dropdownArrow"></i>
                            </button>
                            <div class="dropdown-content absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50 border border-gray-100">
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard
                                    </a>
                                @endif
                                <a href="{{ route('transactions.history') }}" class="block px-4 py-2 text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors">
                                    <i class="fas fa-history mr-2"></i>Riwayat Transaksi
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
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
                            <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-pink-600 transition-colors">Login</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
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
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold text-pink-400 mb-4">
                    <i class="fas fa-heart mr-2"></i>Prelovedbynaz
                </h3>
                <p class="text-gray-400">Marketplace preloved terpercaya dengan kualitas terbaik</p>
                <div class="mt-4 flex justify-center space-x-4">
                    <a href="#" class="text-gray-400 hover:text-pink-400">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-pink-400">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('userDropdown');
            const dropdownButton = dropdown.querySelector('button');
            const dropdownContent = dropdown.querySelector('.dropdown-content');
            const dropdownArrow = document.getElementById('dropdownArrow');

            // Toggle dropdown on click
            dropdownButton.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = dropdownContent.style.opacity === '1';
                
                if (isOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });

            // Open dropdown on hover (desktop only)
            dropdown.addEventListener('mouseenter', function() {
                if (window.innerWidth > 768) {
                    openDropdown();
                }
            });

            // Close dropdown when mouse leaves (desktop only)
            dropdown.addEventListener('mouseleave', function(e) {
                if (window.innerWidth > 768) {
                    // Check if mouse actually left the dropdown area
                    setTimeout(() => {
                        if (!dropdown.matches(':hover')) {
                            closeDropdown();
                        }
                    }, 100);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    closeDropdown();
                }
            });

            // Close dropdown on escape key
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

            // Mobile menu toggle (if you add mobile menu later)
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });

        // Close dropdown when navigating away (for SPA-like behavior)
        document.addEventListener('turbolinks:load', function() {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.querySelector('.dropdown-content').style.opacity = '0';
                dropdown.querySelector('.dropdown-content').style.visibility = 'hidden';
            }
        });
    </script>

    @stack('scripts')
</body>
</html>