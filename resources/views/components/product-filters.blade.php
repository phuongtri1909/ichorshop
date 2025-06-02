<div class="filters-section">
    <!-- Mobile Filter Toggle -->
    <div class="d-block d-lg-none mb-3">
        <button class="btn btn-outline-primary w-100 filter-toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
            <i class="fas fa-filter me-2"></i>
            Filters
        </button>
    </div>

    <!-- Filters Container -->
    <div class="collapse d-lg-block" id="filtersCollapse">
        <div class="filters-container">
            <div class="filters-header d-flex justify-content-between align-items-center">
                <h5 class="filters-title">Filters</h5>
                <button class="btn-close d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse"></button>
            </div>

            <!-- Price Filter -->
            @include('components.filter-price')

            <!-- Colors Filter -->
            @include('components.filter-colors')

            <!-- Size Filter -->
            @include('components.filter-size')

            <!-- Dress Style Filter -->
            @include('components.filter-dress-style')

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
        padding: 20px;
        border: 1px solid #e5e5e5;
        height: fit-content;
    }

    .filter-toggle-btn {
        border-radius: 62px;
        padding: 12px 20px;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        background: white;
    }

    .filter-toggle-btn:hover {
        background: var(--primary-color);
        color: white;
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