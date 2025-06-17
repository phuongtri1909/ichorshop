document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo trạng thái collapse từ localStorage
    initializeCollapseState();
    
    // Xử lý form submission
    setupFilterSubmission();
});

function initializeCollapseState() {
    const filterGroups = document.querySelectorAll('.filter-group');
    
    filterGroups.forEach(group => {
        const filterType = group.getAttribute('data-filter-type');
        if (filterType) {
            // Kiểm tra xem filter này có nên collapse không dựa trên localStorage
            const isCollapsed = localStorage.getItem(`filter_${filterType}_collapsed`) === 'true';
            if (isCollapsed) {
                group.classList.add('collapsed');
            }
            
            // Thêm sự kiện click cho tiêu đề filter
            const filterTitle = group.querySelector('.filter-title');
            if (filterTitle) {
                filterTitle.addEventListener('click', function() {
                    group.classList.toggle('collapsed');
                    // Lưu trạng thái collapse vào localStorage
                    localStorage.setItem(
                        `filter_${filterType}_collapsed`, 
                        group.classList.contains('collapsed')
                    );
                });
            }
        }
    });
}

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
        
        // Bảo tồn search term nếu có
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('q')) {
            const searchInput = document.createElement('input');
            searchInput.type = 'hidden';
            searchInput.name = 'q';
            searchInput.value = urlParams.get('q');
            filterForm.appendChild(searchInput);
        }
        
        // Chỉ thêm giá trị filter nếu nhóm của nó không bị collapse
        // Categories filter
        const categoryFilterGroup = document.querySelector('.filter-group[data-filter-type="categories"]');
        if (categoryFilterGroup && !categoryFilterGroup.classList.contains('collapsed')) {
            categoryCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'categories[]';
                    input.value = checkbox.value;
                    filterForm.appendChild(input);
                }
            });
        }
        
        // Dress styles filter
        const styleFilterGroup = document.querySelector('.filter-group[data-filter-type="styles"]');
        if (styleFilterGroup && !styleFilterGroup.classList.contains('collapsed')) {
            styleCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'styles[]';
                    input.value = checkbox.value;
                    filterForm.appendChild(input);
                }
            });
        }
        
        // Colors filter
        const colorFilterGroup = document.querySelector('.filter-group[data-filter-type="colors"]');
        if (colorFilterGroup && !colorFilterGroup.classList.contains('collapsed')) {
            colorOptions.forEach(option => {
                if (option.classList.contains('active')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'colors[]';
                    input.value = option.getAttribute('data-color');
                    filterForm.appendChild(input);
                }
            });
        }
        
        // Sizes filter
        const sizeFilterGroup = document.querySelector('.filter-group[data-filter-type="sizes"]');
        if (sizeFilterGroup && !sizeFilterGroup.classList.contains('collapsed')) {
            sizeOptions.forEach(option => {
                if (option.classList.contains('active')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'sizes[]';
                    input.value = option.getAttribute('data-size');
                    filterForm.appendChild(input);
                }
            });
        }
        
        // Price filter
        const priceFilterGroup = document.querySelector('.filter-group[data-filter-type="price"]');
        if (priceFilterGroup && !priceFilterGroup.classList.contains('collapsed') && priceMinInput && priceMaxInput) {
            // Chỉ thêm giá trị price nếu không phải giá trị mặc định
            const minPrice = parseInt(priceMinInput.value);
            const maxPrice = parseInt(priceMaxInput.value);
            const minDefault = parseInt(priceMinInput.min);
            const maxDefault = parseInt(priceMaxInput.max);
            
            // Chỉ thêm price nếu không phải khoảng mặc định
            if (minPrice > minDefault || maxPrice < maxDefault) {
                const minInput = document.createElement('input');
                minInput.type = 'hidden';
                minInput.name = 'price_min';
                minInput.value = minPrice;
                filterForm.appendChild(minInput);
                
                const maxInput = document.createElement('input');
                maxInput.type = 'hidden';
                maxInput.name = 'price_max';
                maxInput.value = maxPrice;
                filterForm.appendChild(maxInput);
            }
        }
        
        // Sort option
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
            // Xóa tất cả input hiện tại trong form
            filterForm.innerHTML = '';
            
            // Thêm sort option mới
            const sortInput = document.createElement('input');
            sortInput.type = 'hidden';
            sortInput.name = 'sort';
            sortInput.value = this.value;
            filterForm.appendChild(sortInput);
            
            // Thêm lại các filter hiện tại, chỉ khi nhóm không bị collapse
            const currentUrl = new URL(window.location.href);
            
            // Bảo tồn search term
            if (currentUrl.searchParams.has('q')) {
                const searchInput = document.createElement('input');
                searchInput.type = 'hidden';
                searchInput.name = 'q';
                searchInput.value = currentUrl.searchParams.get('q');
                filterForm.appendChild(searchInput);
            }
            
            // Xử lý categories nếu không collapse
            const categoryFilterGroup = document.querySelector('.filter-group[data-filter-type="categories"]');
            if (categoryFilterGroup && !categoryFilterGroup.classList.contains('collapsed') && 
                currentUrl.searchParams.has('categories[]')) {
                
                currentUrl.searchParams.getAll('categories[]').forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'categories[]';
                    input.value = value;
                    filterForm.appendChild(input);
                });
            }
            
            // Xử lý styles nếu không collapse
            const styleFilterGroup = document.querySelector('.filter-group[data-filter-type="styles"]');
            if (styleFilterGroup && !styleFilterGroup.classList.contains('collapsed') && 
                currentUrl.searchParams.has('styles[]')) {
                
                currentUrl.searchParams.getAll('styles[]').forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'styles[]';
                    input.value = value;
                    filterForm.appendChild(input);
                });
            }
            
            // Xử lý colors nếu không collapse
            const colorFilterGroup = document.querySelector('.filter-group[data-filter-type="colors"]');
            if (colorFilterGroup && !colorFilterGroup.classList.contains('collapsed') && 
                currentUrl.searchParams.has('colors[]')) {
                
                currentUrl.searchParams.getAll('colors[]').forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'colors[]';
                    input.value = value;
                    filterForm.appendChild(input);
                });
            }
            
            // Xử lý sizes nếu không collapse
            const sizeFilterGroup = document.querySelector('.filter-group[data-filter-type="sizes"]');
            if (sizeFilterGroup && !sizeFilterGroup.classList.contains('collapsed') && 
                currentUrl.searchParams.has('sizes[]')) {
                
                currentUrl.searchParams.getAll('sizes[]').forEach(value => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'sizes[]';
                    input.value = value;
                    filterForm.appendChild(input);
                });
            }
            
            // Xử lý price nếu không collapse
            const priceFilterGroup = document.querySelector('.filter-group[data-filter-type="price"]');
            if (priceFilterGroup && !priceFilterGroup.classList.contains('collapsed') && 
                currentUrl.searchParams.has('price_min') && currentUrl.searchParams.has('price_max')) {
                
                const minInput = document.createElement('input');
                minInput.type = 'hidden';
                minInput.name = 'price_min';
                minInput.value = currentUrl.searchParams.get('price_min');
                filterForm.appendChild(minInput);
                
                const maxInput = document.createElement('input');
                maxInput.type = 'hidden';
                maxInput.name = 'price_max';
                maxInput.value = currentUrl.searchParams.get('price_max');
                filterForm.appendChild(maxInput);
            }
            
            // Submit form
            filterForm.submit();
        });
    }
    
    // Khởi tạo giá trị từ URL
    initializeFiltersFromUrl();
}

function initializeFiltersFromUrl() {
    const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]');
    const styleCheckboxes = document.querySelectorAll('input[name="styles[]"]');
    const colorOptions = document.querySelectorAll('.color-option');
    const sizeOptions = document.querySelectorAll('.size-option');
    const priceMinInput = document.getElementById('priceMin');
    const priceMaxInput = document.getElementById('priceMax');
    const sortSelect = document.querySelector('.sort-select');
    
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