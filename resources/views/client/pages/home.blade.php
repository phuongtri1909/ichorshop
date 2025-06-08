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
        'products' => $newProducts
    ])

    <hr class="container my-0 color-primary-5">

    @include('components.list_product_home', [
        'title' => 'TOP SELLING',
        'products' => $topSellingProducts
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
                        <img src="https://picsum.photos/700/400?random=14" alt="Fashion Blog" class="img-fluid rounded-5">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="blog-content ps-lg-5">
                        <h3 class="blog-title">How to combine your daily outfit to looks fresh and cool.</h3>
                        <p class="blog-description color-3">These are ten easy tips to help fashion in New York city from
                            business
                            dresses, evening dresses to casual dresses and formal dresses. Find the perfect fit for your
                            personality.</p>
                        <a href="#" class="btn blog-btn">READ MORE</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    @include('components.testimonials', [
        'testimonials' => [
            [
                'name' => 'Alex K. ✓',
                'rating' => 5,
                'text' =>
                    '"Finding clothes that align with my personal style used to be a challenge until I discovered Shop.co. The range of options they offer is truly remarkable."',
            ],
            [
                'name' => 'James L. ✓',
                'rating' => 5,
                'text' =>
                    '"As someone who\'s always on the lookout for unique fashion pieces, I\'m thrilled to have stumbled upon Shop.co. The selection of clothes is not only diverse but also on-point with the latest trends."',
            ],
            [
                'name' => 'Sarah M. ✓',
                'rating' => 5,
                'text' =>
                    '"I\'m blown away by the quality and style of the clothes I received from Shop.co. From casual wear to elegant dresses, every piece I\'ve bought has exceeded my expectations."',
            ],
            [
                'name' => 'Emily R. ✓',
                'rating' => 5,
                'text' =>
                    '"Shop.co has become my go-to destination for fashion. The variety of styles available means I can always find something that suits my mood and occasion."',
            ],
            [
                'name' => 'Michael T. ✓',
                'rating' => 5,
                'text' =>
                    '"I appreciate the attention to detail in the clothing offered by Shop.co. The fabrics are high-quality, and the designs are both trendy and timeless."',
            ],
            [
                'name' => 'Olivia S. ✓',
                'rating' => 5,
                'text' =>
                    '"Shopping at Shop.co has transformed my wardrobe. The clothes are not only stylish but also comfortable, making them perfect for everyday wear."',
            ],
        ],
    ])

@endsection
