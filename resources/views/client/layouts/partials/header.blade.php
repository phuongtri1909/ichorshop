<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Home')</title>
    <meta name="description" content="@yield('description', 'Truyện Pink Novel - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
    <meta name="keywords" content="@yield('keywords', 'truyện, tiểu thuyết, truyện tranh, đọc truyện online')">
    <meta name="robots" content="noindex, nofollow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Home')">
    <meta property="og:description" content="@yield('description', 'Truyện Pink Novel - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:image" content="{{ $logoPath }}">
    <meta property="og:image:secure_url" content="{{ $logoPath }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="@yield('title', 'Home')">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Home')">
    <meta name="twitter:description" content="@yield('description', 'Truyện Pink Novel - Đọc truyện online, tiểu thuyết, truyện tranh, tiểu thuyết hay nhất')">
    <meta name="twitter:image" content="{{ $logoPath }}">
    <meta name="twitter:image:alt" content="@yield('title', 'Home')">
    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon">
    <meta name="author" content="Truyện pink novel">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="google-site-verification" content="" />

    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "url": "{{ url('/') }}",
          "logo": "{{ $logoPath }}"
        }
    </script>

    @stack('meta')

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inria+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->

    {{-- styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles_shop.css') }}">

    @stack('styles')

    {{-- end styles --}}
</head>

<body>
    @props([
        'type' => 'default',
        'showPromo' => true,
        'showNavigation' => true,
        'showSearch' => true,
        'logoSrc' => null,
    ])

    @php
        $headerClasses = match ($type) {
            'minimal' => 'shop-header minimal-header',
            'checkout' => 'shop-header checkout-header',
            'success' => 'shop-header success-header',
            default => 'shop-header',
        };
    @endphp

    <header class="{{ $headerClasses }}">
        @if ($showPromo && $type !== 'minimal' && !auth()->check())
            <!-- Promo Banner -->
            <div class="promo-banner d-none" id="promoBanner">
                <div class="container-fluid">
                    <span>Sign up and get 20% off to your first order. <a href="#" class="promo-link">Sign Up
                            Now</a></span>
                    <button class="close-banner" onclick="closePromoBanner()">×</button>
                </div>
            </div>
        @endif

        <!-- Main Header -->
        <div class="main-header">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Mobile Menu Button -->
                    @if ($showNavigation)
                        <button class="mobile-menu-btn d-lg-none" onclick="toggleMobileNav()">
                            <i class="fas fa-bars"></i>
                        </button>
                    @endif

                    <!-- Logo -->
                    <a class="navbar-brand p-0" href="{{ route('home') }}">
                        <img height="70" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
                    </a>

                    <!-- Navigation -->
                    @if ($showNavigation && $type !== 'minimal')
                        <nav class="main-nav d-none d-lg-flex ">
                            <div class="nav-dropdown">
                                <a href="#" class="nav-link-custom font-regular">
                                    Shop <i class="fas fa-chevron-down dropdown-arrow"></i>
                                </a>
                            </div>
                            <a href="#" class="nav-link-custom font-regular">On Sale</a>
                            <a href="#" class="nav-link-custom font-regular">New Arrivals</a>
                            <a href="#" class="nav-link-custom font-regular">Brands</a>
                        </nav>
                    @endif

                    <!-- Search -->
                    @if ($showSearch && $type !== 'checkout' && $type !== 'success')
                        <div class="search-container-custom d-none d-md-block">
                            <form>
                                <input type="text" class="search-input-custom"
                                    placeholder="Search for products...">
                                <button type="submit" class="search-btn-custom">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="header-actions">
                        @if ($showSearch && ($type === 'checkout' || $type === 'success'))
                            <!-- Minimal actions for checkout/success -->
                        @else
                            <button class="action-btn d-md-none mt-1">
                                <i class="fas fa-search"></i>
                            </button>
                        @endif

                        <button class="action-btn">
                            <img src="{{ asset('assets/images/svg/cart.svg') }}" alt="Cart">
                            <span class="cart-badge">0</span>
                        </button>
                        <button class="action-btn mt-0 mt-md-1">
                            <i class="fa-regular fa-circle-user"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    @if ($showNavigation && $type !== 'minimal')
        <div class="mobile-nav-overlay" onclick="closeMobileNav()"></div>
        <div class="mobile-nav">
            <div class="mobile-nav-header">     
                <button class="mobile-nav-close" onclick="closeMobileNav()">×</button>
            </div>

            @if ($showSearch)
                <div class="mobile-search">
                    <form class="position-relative">
                        <input type="text" class="search-input-custom" placeholder="Search for products...">
                        <button type="submit" class="search-btn-custom">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            @endif

            <nav>
                <a href="#" class="mobile-nav-item">Shop</a>
                <a href="#" class="mobile-nav-item">On Sale</a>
                <a href="#" class="mobile-nav-item">New Arrivals</a>
                <a href="#" class="mobile-nav-item">Brands</a>
            </nav>
        </div>

        @once
            <script>
            function toggleMobileNav() {
                document.querySelector('.mobile-nav-overlay').classList.add('show');
                document.querySelector('.mobile-nav').classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileNav() {
                document.querySelector('.mobile-nav-overlay').classList.remove('show');
                document.querySelector('.mobile-nav').classList.remove('show');
                document.body.style.overflow = '';
            }

            function closePromoBanner() {
                const promoBanner = document.getElementById('promoBanner');
                if (promoBanner) {
                    promoBanner.remove();
                    localStorage.setItem('promoBannerClosed', 'true');
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const promoBanner = document.getElementById('promoBanner');
                
                if (promoBanner) {
                    if (localStorage.getItem('promoBannerClosed') === 'true') {
                        promoBanner.remove();
                    } else {
                        promoBanner.classList.remove('d-none');
                    }
                }
            });
            </script>
        @endonce
    @endif
