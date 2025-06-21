@extends('client.layouts.app')
@section('title', 'Home - Ichor Shop')
@section('description',
    'Find clothes that matches your style. Browse through our diverse range of meticulously crafted
    garments.')
@section('keywords', 'fashion, clothes, style, shopping, ecommerce')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles_home.css') }}">
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section"
        style="background: url({{ asset('assets/images/dev/hero-couple.png') }}) center/cover no-repeat;">
        <div class="container-fluid">
            <div class="row align-items-center min-vh-100">
                <div class="col-md-8 col-lg-6 col-12">
                    <div class="hero-content p-0 p-sm-4 p-lg-5">
                        <h2 class="hero-title fw-semibold text-start">FIND CLOTHES THAT MATCHES YOUR STYLE</h2>
                        <p class="hero-description my-4 text-start">Browse through our diverse range of meticulously crafted
                            garments,
                            designed to bring out your individuality and cater to your sense of style.</p>

                        <a href="" class="btn hero-btn w-auto">Shop Now</a>

                        <div class="hero-stats">
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="stat-item text-center">
                                        <h3 class="stat-number">{{ $brandCount }} + </h3>
                                        <p class="stat-label">International Brands</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item text-center position-relative">
                                        <div class="stat-line-left"></div>
                                        <div class="stat-line-right"></div>
                                        <h3 class="stat-number">{{ $productCount }} + </h3>
                                        <p class="stat-label">High-Quality Products</p>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item text-center">
                                        <h3 class="stat-number">{{ $customerCount }} + </h3>
                                        <p class="stat-label">Happy Customers</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-hero-image-mobile">
                    <div class="hero-image-mobile">
                        <img src="{{ asset('assets/images/dev/hero-couple-mobile.png') }}" alt="Fashion Couple"
                            class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <section class="brands-section py-4 my-lg-5">
        <div class="container">
            <div class="brands-carousel">
                <div class="brands-track">
                    @foreach ($brands as $item)
                        <div class="brand-item">
                            <img src="{{ Storage::url($item->logo) }}" alt="{{ $item->name }}" class="brand-logo">
                        </div>
                    @endforeach
                    <!-- Duplicate brands for seamless loop -->
                    @foreach ($brands as $item)
                        <div class="brand-item">
                            <img src="{{ Storage::url($item->logo) }}" alt="{{ $item->name }}" class="brand-logo">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('components.list_product_home', [
        'title' => 'NEW ARRIVALS',
        'products' => $newProducts,
        'routeName' => 'new.arrivals',
    ])

    <hr class="container my-0 color-primary-5">

    @include('components.list_product_home', [
        'title' => 'TOP SELLING',
        'products' => $topSellingProducts,
        'routeName' => 'top.selling',
    ])

    <!-- Browse by Style Section -->
    @include('components.style', [
        'styles' => $styles
    ])

    <!-- Features Section -->
    @include('components.features', [
        ($features = [
            [
                'icon' => '<i class="bi bi-currency-dollar-slash"></i>',
                'title' => 'NO Die & plate charges',
                'desc' => 'Lorem ipsum det, cowec tetur duis necgi det...',
            ],
            [
                'icon' => '<i class="bi bi-printer"></i>',
                'title' => 'High quality offset printing',
                'desc' => 'Lorem ipsum det, cowec tetur duis necgi det...',
            ],
            [
                'icon' => '<i class="bi bi-shield-check"></i>',
                'title' => 'Secure payment',
                'desc' => 'Lorem ipsum det, cowec tetur duis necgi det...',
            ],
            [
                'icon' => '<i class="bi bi-sliders"></i>',
                'title' => 'Custom size & style',
                'desc' => 'Lorem ipsum det, cowec tetur duis necgi det...',
            ],
            [
                'icon' => '<i class="bi bi-truck"></i>',
                'title' => 'Fast & free delivery',
                'desc' => 'Lorem ipsum det, cowec tetur duis necgi det...',
            ],
            [
                'icon' => '<i class="bi bi-box-seam"></i>',
                'title' => 'Low minimum order quantity',
                'desc' => 'Lorem ipsum det, cowec tetur duis necgi det...',
            ],
        ]),
    ])

    <!-- Blog Section -->
    <section class="blog-section bg-primary-4 pb-5">
        <div class="container">
            <h3 class="py-4">From The Blog</h3>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="blog-image rounded-4">
                        <img src="{{ asset(Storage::url($blogNew->image)) }}" alt="Fashion Blog" class="img-fluid rounded-5">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="blog-content ps-lg-5">
                        <h3 class="blog-title">{{ $blogNew->title }}</h3>
                        <p class="blog-description color-3">
                            {!! Str::limit(strip_tags($blogNew->content), 250) !!}
                        </p>
                        <a href="{{ route('blogs.show', $blogNew->slug) }}" class="btn blog-btn">READ MORE</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    @include('components.testimonials', [
      
    ])

@endsection
