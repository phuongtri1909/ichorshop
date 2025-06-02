@extends('client.layouts.app')
@section('title', 'Casual Shoes - Shop Now')
@section('description', 'Explore our collection of casual shoes. Find the perfect pair for your everyday style. Shop now!')
@section('keywords', 'casual shoes, everyday shoes, comfortable footwear, casual style')

@section('content')
<div class="category-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Casual</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                @include('components.product-filters')
            </div>

            <!-- Products Content -->
            <div class="col-lg-9">
                @include('components.products-grid')
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .category-page {
        padding: 40px 0;
        min-height: calc(100vh - 200px);
    }

    .breadcrumb {
        background: none;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item a {
        color: var(--primary-color-5);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: var(--primary-color);
    }

    .breadcrumb-item.active {
        color: var(--primary-color);
    }

    @media (max-width: 992px) {
        .category-page {
            padding: 20px 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter toggle functionality
    const filterTitles = document.querySelectorAll('.filter-title');
    
    filterTitles.forEach(title => {
        title.addEventListener('click', function() {
            const filterGroup = this.closest('.filter-group');
            filterGroup.classList.toggle('collapsed');
        });
    });

    // Mobile filter toggle
    const filterToggleBtn = document.querySelector('.filter-toggle-btn');
    const filtersCollapse = document.getElementById('filtersCollapse');
    
    if (filterToggleBtn && filtersCollapse) {
        // Close filter overlay when clicking outside
        filtersCollapse.addEventListener('click', function(e) {
            if (e.target === this) {
                bootstrap.Collapse.getInstance(this).hide();
            }
        });
    }
});
</script>
@endpush
@endsection