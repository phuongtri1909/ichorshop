@extends('client.layouts.app')
@section('title', 'Thương hiệu - Ichor Shop')
@section('description', 'Khám phá các thương hiệu nổi tiếng tại Ichor Shop')

@push('styles')
    <style>
        /* Brand page specific styles */
        .brands-hero {
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.3)), url('{{ asset('assets/images/brand-hero.jpg') }}');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            margin-bottom: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .brands-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .brands-hero-content {
            position: relative;
            z-index: 2;
        }

        .brands-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
        }

        .brands-hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards 0.3s;
        }

        .brand-search {
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards 0.5s;
        }

        .brand-search input {
            padding: 15px 25px;
            border-radius: 30px;
            border: none;
            width: 100%;
            font-size: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .brand-search button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .brand-search button:hover {
            background: var(--primary-color-dark);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .featured-brands {
            margin-bottom: 70px;
        }

        .section-title {
            position: relative;
            margin-bottom: 50px;
            text-align: center;
        }

        .section-title h2 {
            font-size: 2.5rem;
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            width: 60%;
            height: 3px;
            background: var(--primary-color);
            bottom: -10px;
            left: 20%;
        }

        .section-title p {
            color: #777;
            max-width: 700px;
            margin: 0 auto;
        }

        .featured-brand-card {
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.5s ease;
            position: relative;
            opacity: 0;
            transform: translateY(30px);
        }

        .featured-brand-card.animated {
            animation: fadeInUp 0.8s forwards;
        }

        .featured-brand-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .featured-brand-card .card-image {
            height: 250px;
            overflow: hidden;
            position: relative;
            background: #f5f5f5;
        }

        .featured-brand-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 1s ease;
        }

        .featured-brand-card:hover .card-image img {
            transform: scale(1.05);
        }

        .featured-brand-card .brand-logo {
            position: absolute;
            bottom: -40px;
            left: 30px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .featured-brand-card:hover .brand-logo {
            transform: scale(1.1);
        }

        .featured-brand-card .brand-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .featured-brand-card .card-content {
            padding: 60px 30px 30px;
        }

        .featured-brand-card .brand-name {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .featured-brand-card .product-count {
            color: #777;
            margin-bottom: 15px;
        }

        .featured-brand-card .brand-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .featured-brand-card .brand-btn:hover {
            background: var(--primary-color-dark);
            transform: translateX(5px);
        }

        .all-brands {
            padding: 70px 0;
            background-color: #f9fafb;
        }

        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }

        .brand-item {
            background: white;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            opacity: 0;
            transform: translateY(20px);
        }

        .brand-item.animated {
            animation: fadeInUp 0.5s forwards;
        }

        .brand-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .brand-item .brand-logo {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .brand-item .brand-logo img {
            max-height: 60px;
            max-width: 100%;
            object-fit: contain;
        }

        .brand-item h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .brand-item p {
            color: #777;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .brand-item .view-brand {
            display: inline-block;
            color: var(--primary-color);
            font-weight: 500;
            text-decoration: none;
            position: relative;
        }

        .brand-item .view-brand::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary-color);
            bottom: -3px;
            left: 0;
            transition: width 0.3s ease;
        }

        .brand-item:hover .view-brand::after {
            width: 100%;
        }

        .brand-letter-nav {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .brand-letter {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 5px;
            font-weight: 500;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }

        .brand-letter:hover,
        .brand-letter.active {
            background: var(--primary-color);
            color: white;
        }

        .brands-by-letter {
            margin-top: 50px;
        }

        .letter-heading {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .brands-hero h1 {
                font-size: 2.5rem;
            }

            .featured-brand-card .card-image {
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            .brands-hero {
                padding: 80px 0;
            }

            .brands-hero h1 {
                font-size: 2rem;
            }

            .brands-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .brands-hero {
                padding: 60px 0;
            }

            .brands-hero h1 {
                font-size: 1.8rem;
            }

            .brand-search input {
                padding: 12px 20px;
            }

            .brand-search button {
                padding: 7px 15px;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Breadcrumb -->
    @include('components.breadcrumb', [
        'items' => $breadcrumbItems,
        'background' => '',
    ])

    <!-- Hero Section -->
    <section class="brands-hero">
        <div class="container brands-hero-content">
            <h1>Discover Premium Brands</h1>
            <p>Explore our curated collection of exclusive brands that define quality and style. Find your perfect match
                from our extensive selection.</p>
            <div class="brand-search">
                <input type="text" id="brandSearch" placeholder="Search for a brand...">
                <button type="button" id="searchButton">Search</button>
            </div>
        </div>
    </section>

    <!-- Featured Brands Section -->
    <section class="featured-brands">
        <div class="container">
            <div class="section-title">
                <h2>Featured Brands</h2>
                <p>Our most popular brands loved by thousands of customers</p>
            </div>

            <div class="row">
                @foreach ($featuredBrands as $index => $brand)
                    <div class="col-md-6 col-lg-4">
                        <div class="featured-brand-card" data-delay="{{ $index * 150 }}">
                            <div class="card-image">
                                <img src="{{ $brand->background_image ?: asset('assets/images/brand-background-default.jpg') }}"
                                    alt="{{ $brand->name }}">
                                <div class="brand-logo">
                                    <img src="{{ $brand->logo ?: asset('assets/images/brand-logo-placeholder.png') }}"
                                        alt="{{ $brand->name }} logo">
                                </div>
                            </div>
                            <div class="card-content">
                                <h3 class="brand-name">{{ $brand->name }}</h3>
                                <p class="product-count">{{ $brand->products_count }} Products</p>
                                <a href="{{ route('brand.products', $brand->slug) }}" class="brand-btn">Explore Brand</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- All Brands Section -->
    <section class="all-brands">
        <div class="container">
            <div class="section-title">
                <h2>All Brands</h2>
                <p>Browse through our extensive collection of premium brands</p>
            </div>

            <div class="brand-letter-nav">
                <a href="#all" class="brand-letter active">All</a>
                @foreach (range('A', 'Z') as $letter)
                    @if ($brands->where('name', 'like', $letter . '%')->count() > 0)
                        <a href="#letter-{{ $letter }}" class="brand-letter">{{ $letter }}</a>
                    @else
                        <span class="brand-letter" style="opacity: 0.4">{{ $letter }}</span>
                    @endif
                @endforeach
            </div>

            <div id="all" class="brands-grid">
                @foreach ($brands as $index => $brand)
                    <div class="brand-item" data-delay="{{ $index * 100 }}">
                        <div class="brand-logo">
                            <img src="{{ $brand->logo ?: asset('assets/images/brand-logo-placeholder.png') }}"
                                alt="{{ $brand->name }}">
                        </div>
                        <h3>{{ $brand->name }}</h3>
                        <a href="{{ route('brand.products', $brand->slug) }}" class="view-brand">View Products</a>
                    </div>
                @endforeach
            </div>

            <!-- Alphabetical listing -->
            @foreach (range('A', 'Z') as $letter)
                @php
                    $brandsWithLetter = $brands->where('name', 'like', $letter . '%');
                @endphp

                @if ($brandsWithLetter->count() > 0)
                    <div id="letter-{{ $letter }}" class="brands-by-letter" style="display: none;">
                        <h3 class="letter-heading">{{ $letter }}</h3>
                        <div class="brands-grid">
                            @foreach ($brandsWithLetter as $index => $brand)
                                <div class="brand-item" data-delay="{{ $index * 100 }}">
                                    <div class="brand-logo">
                                        <img src="{{ $brand->logo ?: asset('assets/images/brand-logo-placeholder.png') }}"
                                            alt="{{ $brand->name }}">
                                    </div>
                                    <h3>{{ $brand->name }}</h3>
                                    <a href="{{ route('brand.products', $brand->slug) }}" class="view-brand">View
                                        Products</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for featured brands
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const delay = entry.target.getAttribute('data-delay') || 0;
                        setTimeout(() => {
                            entry.target.classList.add('animated');
                        }, delay);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.featured-brand-card, .brand-item').forEach(card => {
                observer.observe(card);
            });

            // Brand letter navigation
            const letterLinks = document.querySelectorAll('.brand-letter');
            const brandSections = document.querySelectorAll('.brands-by-letter');
            const allBrandsGrid = document.querySelector('#all');

            letterLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class
                    letterLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    const targetId = this.getAttribute('href');

                    // Hide all sections first
                    allBrandsGrid.style.display = 'none';
                    brandSections.forEach(section => {
                        section.style.display = 'none';
                    });

                    // Show target section
                    if (targetId === '#all') {
                        allBrandsGrid.style.display = 'grid';
                    } else {
                        const targetSection = document.querySelector(targetId);
                        if (targetSection) {
                            targetSection.style.display = 'block';
                        }
                    }
                });
            });

            // Search functionality
            const searchInput = document.getElementById('brandSearch');
            const searchButton = document.getElementById('searchButton');
            const brandItems = document.querySelectorAll('.brand-item');

            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();

                if (searchTerm === '') {
                    brandItems.forEach(item => {
                        item.style.display = 'block';
                    });
                    return;
                }

                brandItems.forEach(item => {
                    const brandName = item.querySelector('h3').textContent.toLowerCase();
                    if (brandName.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Show the "All" tab
                letterLinks.forEach(l => l.classList.remove('active'));
                document.querySelector('a[href="#all"]').classList.add('active');
                allBrandsGrid.style.display = 'grid';
                brandSections.forEach(section => {
                    section.style.display = 'none';
                });
            }

            searchButton.addEventListener('click', performSearch);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
@endpush
