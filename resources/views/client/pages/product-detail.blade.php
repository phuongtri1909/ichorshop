@extends('client.layouts.app')
@section('title', $product['name'] . ' - Ichor Shop')
@section('description', $product['description'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles_product_detail.css') }}">
@endpush

@section('content')
    <!-- Breadcrumb -->
    @include('components.breadcrumb', [
        'items' => $breadcrumbItems,
        'background' => '',
    ])

    <!-- Product Detail Section -->
    @include('components.product-detail-main', [
        'product' => $product
    ])

    <!-- Product Tabs -->
    @include('components.product-tabs', [
        'tabs' => [
            'details' => 'Product Details',
            'reviews' => 'Rating & Reviews',
            'faqs' => 'FAQs',
        ],
        'product' => $product,
        'faqs' => $faqs,
    ])

    <!-- You might also like -->
    @include('components.related-products', [
        'title' => 'You might also like',
        'products' => $likeProducts,
    ])

    <!-- Related products -->
    @include('components.related-products', [
        'title' => 'Related Products',
        'products' => $relatedProducts,
    ])

@endsection
