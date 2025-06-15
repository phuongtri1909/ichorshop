<div class="products-section">
    <!-- Header -->
    <div class="mb-4">
        <div class="results-info">
            <h3 class="section-title-product-grid">{{ $title }}</h3>
        </div>
        <div class="sort-options d-flex align-items-center justify-content-between">
            <p class="results-count">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of
                {{ $products->total() ?? 0 }} Products</p>
            <div class="d-block d-lg-none">
                <button class="filter-toggle-btn" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filtersCollapse">
                    <img src="{{ asset('assets/images/svg/filter.svg') }}" alt="">
                </button>
            </div>
            <div class="d-none d-lg-flex align-items-center">
                <span class="sort-label me-2">Sort by:</span>
                <select class="sort-select">
                    <option value="most-popular" {{ request('sort') == 'most-popular' ? 'selected' : '' }}>Most Popular
                    </option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High
                    </option>
                    <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to
                        Low</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                </select>
            </div>
        </div>

        @if (!empty($appliedFilters ?? []))
            <div class="applied-filters mt-3">
                @if (isset($appliedFilters['categories']))
                    @foreach ($appliedFilters['categories'] as $catId)
                        @php
                            $category = \App\Models\Category::find($catId);
                        @endphp
                        @if ($category)
                            <div class="filter-tag">
                                {{ $category->name }}
                                <span class="remove-filter" data-filter-type="categories[]"
                                    data-filter-value="{{ $catId }}">&times;</span>
                            </div>
                        @endif
                    @endforeach
                @endif

                @if (isset($appliedFilters['styles']))
                    @foreach ($appliedFilters['styles'] as $styleId)
                        @php
                            $style = \App\Models\DressStyle::find($styleId);
                        @endphp
                        @if ($style)
                            <div class="filter-tag">
                                {{ $style->name }}
                                <span class="remove-filter" data-filter-type="styles[]"
                                    data-filter-value="{{ $styleId }}">&times;</span>
                            </div>
                        @endif
                    @endforeach
                @endif

                @if (isset($appliedFilters['colors']))
                    @foreach ($appliedFilters['colors'] as $color)
                        @php
                            if ($color === null) {
                                $colorName = 'Default';
                                $colorDisplay = 'transparent';
                            } else {
                                $colorName = isset($colors)
                                    ? collect($colors)->firstWhere('value', $color)['name'] ?? $color
                                    : $color;
                                $colorDisplay = $color;
                            }
                        @endphp
                        <div class="filter-tag">
                            @if ($color === null)
                                <span class="d-inline-block me-1"
                                    style="width: 12px; height: 12px; background: linear-gradient(45deg, #f5f5f5, #e0e0e0); border: 1px dashed #ccc; border-radius: 50%;"></span>
                                Default
                            @else
                                <span class="d-inline-block me-1"
                                    style="width: 12px; height: 12px; background-color: {{ $colorDisplay }}; border-radius: 50%; {{ $color === 'white' ? 'border: 1px solid #e5e5e5;' : '' }}"></span>
                                {{ $colorName }}
                            @endif
                            <span class="remove-filter" data-filter-type="colors[]"
                                data-filter-value="{{ $color ?? 'null' }}">&times;</span>
                        </div>
                    @endforeach
                @endif

                @if (isset($appliedFilters['sizes']))
                    @foreach ($appliedFilters['sizes'] as $size)
                        <div class="filter-tag">
                            Size: {{ $size }}
                            <span class="remove-filter" data-filter-type="sizes[]"
                                data-filter-value="{{ $size }}">&times;</span>
                        </div>
                    @endforeach
                @endif

                @if (isset($appliedFilters['price_min']) && isset($appliedFilters['price_max']))
                    <div class="filter-tag">
                        Price: ${{ $appliedFilters['price_min'] }} - ${{ $appliedFilters['price_max'] }}
                        <span class="remove-filter" data-filter-type="price" data-filter-value="">&times;</span>
                    </div>
                @endif

                @if (!empty($appliedFilters))
                    <div class="filter-tag clear-all">
                        Clear All
                        <span class="remove-filter" data-filter-type="all" data-filter-value="">&times;</span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Products Grid -->
    <div class="row">
        @forelse ($products as $product)
            <div class="col-6 col-md-4 mb-4">
                @include('components.item_product', ['product' => $product])
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <img src="{{ asset('assets/images/svg/empty-results.svg') }}" alt="No products found"
                    style="max-width: 150px;">
                <h4 class="mt-3">No products found</h4>
                <p class="text-muted">Try adjusting your filters or search terms.</p>
                <a href="{{ url()->current() }}" class="btn btn-pry mt-3">Clear all filters</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($products->hasPages())
        <div class="pagination-controls">
            {{ $products->links('components.paginate') }}
        </div>
    @endif
</div>

@push('styles')
    <style>
        .filter-toggle-btn {
            border-radius: 50%;
            height: 35px;
            width: 35px;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background: white;
        }

        .filter-toggle-btn:hover {
            background: var(--primary-color);
            color: white !important;
        }

        .filter-toggle-btn:hover img {
            filter: invert(1);
        }

        .section-title-product-grid {
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
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 14px;
            min-width: 150px;
        }

        .sort-select:focus-visible {
            border: none;
            outline-offset: none;
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

        .applied-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .filter-tag {
            background-color: var(--primary-color-2);
            color: var(--primary-color);
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }

        .remove-filter {
            margin-left: 6px;
            cursor: pointer;
            font-size: 14px;
            line-height: 1;
        }

        .filter-tag.clear-all {
            background-color: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .sort-select {
                min-width: auto;
                flex: 1;
                max-width: 200px;
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle remove filter tags
            const removeFilterBtns = document.querySelectorAll('.remove-filter');

            removeFilterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const filterType = this.getAttribute('data-filter-type');
                    const filterValue = this.getAttribute('data-filter-value');

                    // Create form for submission
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.style.display = 'none';
                    document.body.appendChild(form);

                    // Get current URL parameters
                    const urlParams = new URLSearchParams(window.location.search);

                    if (filterType === 'all') {
                        // Just remove page parameter if it exists
                        if (urlParams.has('page')) {
                            const pageInput = document.createElement('input');
                            pageInput.type = 'hidden';
                            pageInput.name = 'page';
                            pageInput.value = '1';
                            form.appendChild(pageInput);
                        }
                    } else if (filterType === 'price') {
                        // Remove price_min and price_max
                        urlParams.delete('price_min');
                        urlParams.delete('price_max');

                        // Add all other parameters
                        urlParams.forEach((value, key) => {
                            if (key !== 'price_min' && key !== 'price_max') {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = key;
                                input.value = value;
                                form.appendChild(input);
                            }
                        });
                    } else {
                        // For array parameters (categories[], colors[], sizes[], styles[])
                        const values = urlParams.getAll(filterType);
                        const newValues = values.filter(val => {
                            if (filterType === 'colors[]' && filterValue === 'null') {
                                return val !== null && val !== 'null';
                            }
                            return val !== filterValue;
                        });

                        // Add all other parameters
                        urlParams.forEach((value, key) => {
                            if (key !== filterType && key !== 'page') {
                                if (key.endsWith('[]')) {
                                    const arrayValues = urlParams.getAll(key);
                                    arrayValues.forEach(val => {
                                        const input = document.createElement(
                                            'input');
                                        input.type = 'hidden';
                                        input.name = key;
                                        input.value = val;
                                        form.appendChild(input);
                                    });
                                } else {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = key;
                                    input.value = value;
                                    form.appendChild(input);
                                }
                            }
                        });

                        // Add filtered values back
                        newValues.forEach(val => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = filterType;
                            input.value = val;
                            form.appendChild(input);
                        });
                    }

                    // Submit form
                    form.submit();
                });
            });
        });
    </script>
@endpush
