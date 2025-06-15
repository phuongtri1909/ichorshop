@extends('client.layouts.app')
@section('title', 'New Arrivals - Shop Now')
@section('description',
    'Discover the latest fashion trends with our new arrivals. Shop now for the freshest styles and
    exclusive collections!')
@section('keywords', 'new arrivals, latest fashion, trendy clothes, exclusive collections')

@section('content')

    @include('components.breadcrumb', [
        'items' => [['title' => 'Home', 'url' => route('home')], ['title' => 'New Arrivals', 'active' => true]],
        'background' => '',
    ])

    <div class="category-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    @include('components.product-filters')
                </div>

                <div class="col-lg-9">
                    @include('components.products-grid', [
                        'title' => 'New Arrivals',
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Đóng/mở các nhóm filter
                const filterTitles = document.querySelectorAll('.filter-title');
                filterTitles.forEach(title => {
                    title.addEventListener('click', function() {
                        const filterGroup = this.closest('.filter-group');
                        filterGroup.classList.toggle('collapsed');
                    });
                });
                
                // Xử lý form submission
                setupFilterSubmission();
            });
            
            function setupFilterSubmission() {
                // Lấy tất cả các filter controls
                const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]');
                const styleCheckboxes = document.querySelectorAll('input[name="styles[]"]');
                const colorOptions = document.querySelectorAll('.color-option');
                const sizeOptions = document.querySelectorAll('.size-option');
                const priceMinInput = document.getElementById('priceMin');
                const priceMaxInput = document.getElementById('priceMax');
                const sortSelect = document.querySelector('.sort-select');
                const filterForm = document.createElement('form');
                const applyFilterBtn = document.querySelector('.apply-filter-btn');
                
                // Thiết lập form ẩn
                filterForm.method = 'GET';
                filterForm.style.display = 'none';
                document.body.appendChild(filterForm);
                
                // Xử lý sự kiện click vào nút Apply Filter
                applyFilterBtn.addEventListener('click', function() {
                    // Xóa tất cả input hiện tại trong form
                    filterForm.innerHTML = '';
                    
                    // Thêm các input cho categories đã chọn
                    categoryCheckboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'categories[]';
                            input.value = checkbox.value;
                            filterForm.appendChild(input);
                        }
                    });
                    
                    // Thêm các input cho dress styles đã chọn
                    styleCheckboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'styles[]';
                            input.value = checkbox.value;
                            filterForm.appendChild(input);
                        }
                    });
                    
                    // Thêm các input cho colors đã chọn
                    colorOptions.forEach(option => {
                        if (option.classList.contains('active')) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'colors[]';
                            input.value = option.getAttribute('data-color');
                            filterForm.appendChild(input);
                        }
                    });
                    
                    // Thêm các input cho sizes đã chọn
                    sizeOptions.forEach(option => {
                        if (option.classList.contains('active')) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'sizes[]';
                            input.value = option.getAttribute('data-size');
                            filterForm.appendChild(input);
                        }
                    });
                    
                    // Thêm input cho price range
                    if (priceMinInput && priceMaxInput) {
                        const minInput = document.createElement('input');
                        minInput.type = 'hidden';
                        minInput.name = 'price_min';
                        minInput.value = priceMinInput.value;
                        filterForm.appendChild(minInput);
                        
                        const maxInput = document.createElement('input');
                        maxInput.type = 'hidden';
                        maxInput.name = 'price_max';
                        maxInput.value = priceMaxInput.value;
                        filterForm.appendChild(maxInput);
                    }
                    
                    // Thêm input cho sort option nếu đã chọn
                    if (sortSelect && sortSelect.value) {
                        const sortInput = document.createElement('input');
                        sortInput.type = 'hidden';
                        sortInput.name = 'sort';
                        sortInput.value = sortSelect.value;
                        filterForm.appendChild(sortInput);
                    }
                    
                    // Submit form
                    filterForm.submit();
                });
                
                // Xử lý sự kiện thay đổi sort option
                if (sortSelect) {
                    sortSelect.addEventListener('change', function() {
                        // Tạo input cho sort option
                        const sortInput = document.createElement('input');
                        sortInput.type = 'hidden';
                        sortInput.name = 'sort';
                        sortInput.value = this.value;
                        
                        // Xóa tất cả input hiện tại trong form
                        filterForm.innerHTML = '';
                        
                        // Thêm tất cả filter hiện tại vào form
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.forEach((value, key) => {
                            if (key !== 'sort' && key !== 'page') {
                                if (key.endsWith('[]')) {
                                    const values = currentUrl.searchParams.getAll(key);
                                    values.forEach(val => {
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = key;
                                        input.value = val;
                                        filterForm.appendChild(input);
                                    });
                                } else {
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = key;
                                    input.value = value;
                                    filterForm.appendChild(input);
                                }
                            }
                        });
                        
                        // Thêm sort option mới
                        filterForm.appendChild(sortInput);
                        
                        // Submit form
                        filterForm.submit();
                    });
                }
                
                // Khởi tạo các filter đã áp dụng từ URL
                const urlParams = new URLSearchParams(window.location.search);
                
                // Khởi tạo categories
                if (urlParams.has('categories[]')) {
                    const selectedCategories = urlParams.getAll('categories[]');
                    categoryCheckboxes.forEach(checkbox => {
                        if (selectedCategories.includes(checkbox.value)) {
                            checkbox.checked = true;
                        }
                    });
                }
                
                // Khởi tạo styles
                if (urlParams.has('styles[]')) {
                    const selectedStyles = urlParams.getAll('styles[]');
                    styleCheckboxes.forEach(checkbox => {
                        if (selectedStyles.includes(checkbox.value)) {
                            checkbox.checked = true;
                        }
                    });
                }
                
                // Khởi tạo colors
                if (urlParams.has('colors[]')) {
                    const selectedColors = urlParams.getAll('colors[]');
                    colorOptions.forEach(option => {
                        if (selectedColors.includes(option.getAttribute('data-color'))) {
                            option.classList.add('active');
                        }
                    });
                }
                
                // Khởi tạo sizes
                if (urlParams.has('sizes[]')) {
                    const selectedSizes = urlParams.getAll('sizes[]');
                    sizeOptions.forEach(option => {
                        if (selectedSizes.includes(option.getAttribute('data-size'))) {
                            option.classList.add('active');
                        }
                    });
                }
                
                // Khởi tạo price range
                if (urlParams.has('price_min') && urlParams.has('price_max') && priceMinInput && priceMaxInput) {
                    priceMinInput.value = urlParams.get('price_min');
                    priceMaxInput.value = urlParams.get('price_max');
                    document.getElementById('minPrice').textContent = urlParams.get('price_min');
                    document.getElementById('maxPrice').textContent = urlParams.get('price_max');
                }
                
                // Khởi tạo sort option
                if (urlParams.has('sort') && sortSelect) {
                    sortSelect.value = urlParams.get('sort');
                }
            }
        </script>
    @endpush
@endsection
