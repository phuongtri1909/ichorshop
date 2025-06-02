<div class="products-section">
    <!-- Header -->
    <div class="products-header d-flex justify-content-between align-items-center mb-4">
        <div class="results-info">
            <h3 class="section-title">Casual</h3>
            <p class="results-count">Showing 1-10 of 100 Products</p>
        </div>
        <div class="sort-options d-flex align-items-center">
            <span class="sort-label me-2">Sort by:</span>
            <select class="form-select sort-select">
                <option value="most-popular">Most Popular</option>
                <option value="newest">Newest</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="rating">Highest Rated</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="products-grid">
        @php
            $products = [
                ['name' => 'Gradient Graphic T-shirt', 'price' => 145, 'original_price' => null, 'rating' => 3.5, 'image' => 'https://picsum.photos/300/400?random=1'],
                ['name' => 'Polo with Tipping Details', 'price' => 180, 'original_price' => null, 'rating' => 4.5, 'image' => 'https://picsum.photos/300/400?random=2'],
                ['name' => 'Black Striped T-shirt', 'price' => 120, 'original_price' => 150, 'discount' => 30, 'rating' => 5.0, 'image' => 'https://picsum.photos/300/400?random=3'],
                ['name' => 'Skinny Fit Jeans', 'price' => 240, 'original_price' => 260, 'discount' => 20, 'rating' => 3.5, 'image' => 'https://picsum.photos/300/400?random=4'],
                ['name' => 'Checkered Shirt', 'price' => 180, 'original_price' => null, 'rating' => 4.5, 'image' => 'https://picsum.photos/300/400?random=5'],
                ['name' => 'Sleeve Striped T-shirt', 'price' => 130, 'original_price' => 160, 'discount' => 30, 'rating' => 4.5, 'image' => 'https://picsum.photos/300/400?random=6'],
                ['name' => 'Vertical Striped Shirt', 'price' => 212, 'original_price' => 232, 'discount' => 20, 'rating' => 5.0, 'image' => 'https://picsum.photos/300/400?random=7'],
                ['name' => 'Courage Graphic T-shirt', 'price' => 145, 'original_price' => null, 'rating' => 4.0, 'image' => 'https://picsum.photos/300/400?random=8'],
                ['name' => 'Loose Fit Bermuda Shorts', 'price' => 80, 'original_price' => null, 'rating' => 3.0, 'image' => 'https://picsum.photos/300/400?random=9']
            ];
        @endphp

        @foreach($products as $product)
            @include('components.item_product', ['product' => $product])
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="pagination-section mt-5">
        <nav aria-label="Products pagination">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <i class="fas fa-chevron-left"></i>
                        <span class="ms-1">Previous</span>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><span class="page-link">...</span></li>
                <li class="page-item"><a class="page-link" href="#">8</a></li>
                <li class="page-item"><a class="page-link" href="#">9</a></li>
                <li class="page-item"><a class="page-link" href="#">10</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span class="me-1">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

@push('styles')
<style>
    .products-header {
        border-bottom: 1px solid #e5e5e5;
        padding-bottom: 20px;
    }

    .section-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 5px;
    }

    .results-count {
        color: var(--primary-color-5);
        margin: 0;
        font-size: 14px;
    }

    .sort-label {
        color: var(--primary-color-5);
        font-size: 14px;
    }

    .sort-select {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 14px;
        min-width: 150px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .pagination {
        --bs-pagination-border-color: #e5e5e5;
        --bs-pagination-active-bg: var(--primary-color);
        --bs-pagination-active-border-color: var(--primary-color);
    }

    .page-link {
        color: var(--primary-color-5);
        padding: 12px 16px;
        border-radius: 8px;
        margin: 0 2px;
        border: 1px solid #e5e5e5;
    }

    .page-link:hover {
        color: var(--primary-color);
        background-color: var(--primary-color-2);
        border-color: var(--primary-color);
    }

    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    @media (max-width: 768px) {
        .products-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start !important;
        }

        .sort-options {
            width: 100%;
            justify-content: space-between;
        }

        .sort-select {
            min-width: auto;
            flex: 1;
            max-width: 200px;
        }

        .products-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .pagination {
            font-size: 14px;
        }

        .page-link {
            padding: 8px 12px;
        }
    }
</style>
@endpush