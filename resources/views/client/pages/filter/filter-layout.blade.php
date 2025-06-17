@extends('client.layouts.app')

@section('content')
    @include('components.breadcrumb', [
        'items' => $breadcrumbItems ?? [['title' => 'Home', 'url' => route('home')], ['title' => $title ?? 'Filter', 'active' => true]],
        'background' => '',
    ])

    <div class="category-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    @include('components.product-filters', [
                        'showCategoryFilter' => $showCategoryFilter ?? true
                    ])
                </div>

                <div class="col-lg-9">
                    @include('components.products-grid', [
                        'title' => $title ?? 'Products',
                        'products' => $products,
                    ])
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .applied-filters {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 20px;
            }
            
            .filter-tag {
                background: var(--primary-color-2);
                color: var(--primary-color);
                border-radius: 20px;
                padding: 5px 12px;
                font-size: 12px;
                display: flex;
                align-items: center;
            }
            
            .filter-tag .remove-filter {
                margin-left: 5px;
                cursor: pointer;
                font-size: 14px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/product-filters.js') }}"></script>
    @endpush
@endsection