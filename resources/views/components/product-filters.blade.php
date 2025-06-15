<div class="filters-section border-0 border-lg-4 rounded-4 p-0 p-lg-3">
    <div class="collapse d-lg-block" id="filtersCollapse">
        <div class="filters-container">
            <div class="filters-header d-flex justify-content-between align-items-center">
                <h5 class="filters-title">Filters</h5>
                <img src="{{ asset('assets/images/svg/filter.svg') }}" alt="">
            </div>

            <!-- Category Filter -->
            @include('components.filter-categories', ['categories' => $categories])

            <!-- Price Filter -->
            @include('components.filter-price',['priceRange' => $priceRange])

            <!-- Colors Filter -->
            @include('components.filter-colors', ['colors' => $colors])

            <!-- Size Filter -->
            @include('components.filter-size', ['sizes' => $sizes])

            <!-- Dress Style Filter -->
            @include('components.filter-dress-style', ['dressStyles' => $dressStyles])

            <!-- Apply Filter Button -->
            <div class="filter-actions mt-4">
                <button class="btn apply-filter-btn w-100">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .filters-section {
        background: white;
        border-radius: 20px;
        height: fit-content;
    }

    .filters-header {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e5e5e5;
    }

    .filters-title {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0;
    }

    .apply-filter-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 62px;
        padding: 14px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .apply-filter-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
    }

    @media (max-width: 992px) {
        .filters-section {
            border-radius: 0;
            border-left: none;
            border-right: none;
            margin: 0 -15px;
        }


        #filtersCollapse {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 1050;
            padding: 20px;
            overflow-y: auto;
        }

        #filtersCollapse.show {
            display: block !important;
        }
    }
</style>
@endpush