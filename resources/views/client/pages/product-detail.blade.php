@extends('client.layouts.app')
@section('title', 'One Life Graphic T-shirt - Ichor Shop')
@section('description', 'This graphic t-shirt which is perfect for any occasion. Crafted from a soft and breathable
    fabric, it offers superior comfort and style.')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/styles_product_detail.css') }}">
    @endpush

@section('content')
    <!-- Breadcrumb -->
    @include('components.breadcrumb', [
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'Shop', 'url' => ''],
            ['title' => 'Men', 'url' => ''],
            ['title' => 'T-shirts', 'url' => null, 'active' => true],
        ],
        'background' => '',
    ])

    <!-- Product Detail Section -->
    @include('components.product-detail-main', [
        'product' => [
            'name' => 'One Life Graphic T-shirt',
            'rating' => 4.5,
            'reviews_count' => 451,
            'current_price' => 260,
            'original_price' => 300,
            'discount' => 40,
            'description' =>
                'This graphic t-shirt which is perfect for any occasion. Crafted from a soft and breathable fabric, it offers superior comfort and style.',
            'images' => [
                'https://picsum.photos/300/400?random=1',
                'https://picsum.photos/300/400?random=2',
                'https://picsum.photos/300/400?random=3',
                'https://picsum.photos/300/400?random=4',
            ],
            'colors' => ['brown', 'green', 'navy'],
            'sizes' => ['Small', 'Medium', 'Large', 'X-Large'],
        ],
    ])

    <!-- Product Tabs -->
    @include('components.product-tabs', [
        'tabs' => [
            'details' => 'Product Details',
            'reviews' => 'Rating & Reviews',
            'faqs' => 'FAQs',
        ],
    ])

    <!-- You might also like -->
    @include('components.related-products', [
        'title' => 'You might also like',
        'products' => [
            [
                'image' => 'https://picsum.photos/300/400?random=20',
                'name' => 'Polo with Contrast Trims',
                'rating' => '4.0/5',
                'current_price' => '$212',
                'original_price' => '$242',
                'discount' => '-10%',
            ],
            [
                'image' => 'https://picsum.photos/300/400?random=21',
                'name' => 'Gradient Graphic T-shirt',
                'rating' => '3.5/5',
                'current_price' => '$145',
            ],
            [
                'image' => 'https://picsum.photos/300/400?random=22',
                'name' => 'Polo with Tipping Details',
                'rating' => '4.5/5',
                'current_price' => '$180',
            ],
            [
                'image' => 'https://picsum.photos/300/400?random=23',
                'name' => 'Black Striped T-shirt',
                'rating' => '5.0/5',
                'current_price' => '$120',
                'original_price' => '$150',
                'discount' => '-30%',
            ]
        ]
    ])

    {{-- related products --}}
    @include('components.related-products', [
        'title' => 'Related Products',
        'products' => [
            [
                'image' => 'https://picsum.photos/300/400?random=20',
                'name' => 'Polo with Contrast Trims',
                'rating' => '4.0/5',
                'current_price' => '$212',
                'original_price' => '$242',
                'discount' => '-10%',
            ],
            [
                'image' => 'https://picsum.photos/300/400?random=21',
                'name' => 'Gradient Graphic T-shirt',
                'rating' => '3.5/5',
                'current_price' => '$145',
            ],
            [
                'image' => 'https://picsum.photos/300/400?random=22',
                'name' => 'Polo with Tipping Details',
                'rating' => '4.5/5',
                'current_price' => '$180',
            ],
            [
                'image' => 'https://picsum.photos/300/400?random=23',
                'name' => 'Black Striped T-shirt',
                'rating' => '5.0/5',
                'current_price' => '$120',
                'original_price' => '$150',
                'discount' => '-30%',
            ]
        ]
    ])

@endsection
