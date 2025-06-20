@extends('admin.layouts.sidebar')

@section('title', 'Thêm mã giảm giá mới')

@section('main-content')
    <div class="coupon-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">Mã giảm giá</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-tag icon-title"></i>
                    <h5>Thêm mã giảm giá mới</h5>
                </div>
            </div>
            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.coupons.store') }}" method="POST" class="coupon-form" id="coupon-form">
                    @csrf

                    <div class="form-section">
                        <h6 class="section-title">Thông tin cơ bản</h6>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label required">
                                        Mã giảm giá <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" id="code" name="code" class="form-control"
                                            placeholder="Nhập mã giảm giá" value="{{ old('code') }}" required>
                                        <button type="button" id="generate-code" class="btn btn-outline-secondary">
                                            <i class="fas fa-sync-alt"></i> Tạo mã
                                        </button>
                                    </div>
                                    <div class="error-message" id="code-error"></div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label required">
                                        Loại giảm giá <span class="required-asterisk">*</span>
                                    </label>
                                    <select id="type" name="type" class="form-select" required>
                                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                            Giảm theo phần trăm (%)
                                        </option>
                                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>
                                            Giảm số tiền cố định ($)
                                        </option>
                                    </select>
                                    <div class="error-message" id="type-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="value" class="form-label required">
                                        Giá trị giảm giá <span class="required-asterisk">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" id="value" name="value" class="form-control"
                                            placeholder="Nhập giá trị giảm giá" value="{{ old('value') }}" min="0"
                                            step="0.01" required>
                                        <span class="input-group-text" id="value-addon">%</span>
                                    </div>
                                    <div class="error-message" id="value-error"></div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="min_order_amount" class="form-label">
                                        Giá trị đơn hàng tối thiểu
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" id="min_order_amount" name="min_order_amount"
                                            class="form-control" placeholder="0.00" value="{{ old('min_order_amount') }}"
                                            min="0" step="0.01">
                                    </div>
                                    <div class="error-message" id="min_order_amount-error"></div>
                                    <small class="form-text">Để trống nếu không có giá trị tối thiểu</small>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="max-discount-row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="max_discount_amount" class="form-label">
                                        Giảm giá tối đa
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" id="max_discount_amount" name="max_discount_amount"
                                            class="form-control" placeholder="0.00" value="{{ old('max_discount_amount') }}"
                                            min="0" step="0.01">
                                    </div>
                                    <div class="error-message" id="max_discount_amount-error"></div>
                                    <small class="form-text">Áp dụng cho giảm giá theo phần trăm. Để trống nếu không giới
                                        hạn</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Thời gian hiệu lực</h6>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">
                                        Ngày bắt đầu
                                    </label>
                                    <input type="datetime-local" id="start_date" name="start_date" class="form-control"
                                        value="{{ old('start_date') }}">
                                    <div class="error-message" id="start_date-error"></div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">
                                        Ngày kết thúc
                                    </label>
                                    <input type="datetime-local" id="end_date" name="end_date" class="form-control"
                                        value="{{ old('end_date') }}">
                                    <div class="error-message" id="end_date-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="usage_limit" class="form-label">
                                        Giới hạn sử dụng tổng
                                    </label>
                                    <input type="number" id="usage_limit" name="usage_limit" class="form-control"
                                        placeholder="Không giới hạn" value="{{ old('usage_limit') }}" min="0">
                                    <div class="error-message" id="usage_limit-error"></div>
                                    <small class="form-text">Để trống nếu không giới hạn số lần sử dụng</small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="usage_limit_per_user" class="form-label">
                                        Giới hạn sử dụng cho mỗi người dùng
                                    </label>
                                    <input type="number" id="usage_limit_per_user" name="usage_limit_per_user"
                                        class="form-control" placeholder="Không giới hạn"
                                        value="{{ old('usage_limit_per_user') }}" min="0">
                                    <div class="error-message" id="usage_limit_per_user-error"></div>
                                    <small class="form-text">Để trống nếu không giới hạn số lần sử dụng cho mỗi người
                                        dùng</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Áp dụng cho biến thể sản phẩm</h6>

                        <div class="variant-selection">
                            <div class="form-group">
                                <label class="form-label">Chọn sản phẩm và biến thể</label>

                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="product-search"
                                        placeholder="Tìm kiếm sản phẩm theo tên hoặc SKU">
                                    <button class="btn btn-outline-secondary" type="button" id="search-products-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>

                                <div class="products-container" id="products-container">
                                    <div class="text-center py-4" id="products-loading">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                        <div class="mt-2">Đang tải sản phẩm...</div>
                                    </div>
                                    <div id="products-list"></div>
                                    <div class="text-center py-3 d-none" id="load-more-products">
                                        <button class="btn btn-pry btn-sm" type="button">
                                            <i class="fas fa-sync-alt me-1"></i> Tải thêm sản phẩm
                                        </button>
                                    </div>
                                    <div class="text-center py-3 d-none" id="no-products-found">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open me-2"></i>Không tìm thấy sản phẩm nào
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text">Không chọn nếu áp dụng cho tất cả sản phẩm</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Áp dụng cho người dùng</h6>

                        <div class="user-selection">
                            <div class="form-group">
                                <label class="form-label">Chọn người dùng</label>

                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" id="user-search"
                                        placeholder="Tìm kiếm người dùng theo tên hoặc email">
                                    <button class="btn btn-outline-secondary" type="button" id="search-users-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>

                                <div class="alert alert-info mb-3 {{ empty($selectedUserIds ?? []) ? 'd-none' : '' }}">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Đã chọn <span id="selected-users-count">{{ count($selectedUserIds ?? []) }}</span>
                                    người dùng.
                                </div>

                                <div class="users-container" id="users-container">
                                    <div class="text-center py-4" id="users-loading">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="visually-hidden">Đang tải...</span>
                                        </div>
                                        <div class="mt-2">Đang tải người dùng...</div>
                                    </div>
                                    <div id="users-list" class="row"></div>
                                    <div class="text-center py-3 d-none" id="load-more-users">
                                        <button class="btn btn-pry btn-sm" type="button">
                                            <i class="fas fa-sync-alt me-1"></i> Tải thêm người dùng
                                        </button>
                                    </div>
                                    <div class="text-center py-3 d-none" id="no-users-found">
                                        <div class="text-muted">
                                            <i class="fas fa-users me-2"></i>Không tìm thấy người dùng nào
                                        </div>
                                    </div>
                                </div>
                                <small class="form-text">Không chọn nếu áp dụng cho tất cả người dùng</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h6 class="section-title">Thông tin khác</h6>

                        <div class="form-group">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea id="description" name="description" class="form-control" rows="3"
                                placeholder="Nhập mô tả về mã giảm giá">{{ old('description') }}</textarea>
                            <div class="error-message" id="description-error"></div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt mã giảm giá
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.coupons.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu mã giảm giá
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // ===== VARIABLES =====
            // Product variables
            let productPage = 1;
            let hasMoreProducts = true;
            let isProductLoading = false;
            let productSearchTerm = '';
            let selectedVariantIds = [];

            // User variables
            let userPage = 1;
            let hasMoreUsers = true;
            let isUserLoading = false;
            let userSearchTerm = '';
            let selectedUserIds = [];
            let loadedUserIds = [];

            // ===== INITIALIZATION =====
            initializeProductsSection();
            initializeUsersSection();
            updateValueAddon();

            // ===== EVENT HANDLERS =====
            // Product search handlers
            $('#search-products-btn').on('click', function() {
                searchProducts();
            });

            $('#product-search').on('keypress', function(e) {
                if (e.which === 13) {
                    searchProducts();
                    e.preventDefault();
                }
            });

            // User search handlers
            $('#search-users-btn').on('click', function() {
                searchUsers();
            });

            $('#user-search').on('keypress', function(e) {
                if (e.which === 13) {
                    searchUsers();
                    e.preventDefault();
                }
            });

            // Discount type change handler
            $('#type').change(function() {
                updateValueAddon();
            });

            // Generate coupon code handler
            $('#generate-code').click(function() {
                generateCouponCode();
            });

            // Infinite scroll for products
            $('#products-container').on('scroll', function() {
                handleProductScroll($(this));
            });

            // Infinite scroll for users
            $('#users-container').on('scroll', function() {
                handleUserScroll($(this));
            });

            // Load more products button handler
            $('#load-more-products button').on('click', function() {
                loadMoreProducts();
            });

            // Load more users button handler
            $('#load-more-users button').on('click', function() {
                loadMoreUsers();
            });

            // Form submission
            $('#coupon-form').submit(function(e) {
                e.preventDefault();
                submitForm();
            });

            // ===== FUNCTIONS =====
            // --- PRODUCTS SECTION ---
            function initializeProductsSection() {
                loadProducts();
            }

            function updateValueAddon() {
                const type = $('#type').val();
                if (type === 'percentage') {
                    $('#value-addon').text('%');
                    $('#max-discount-row').show();
                } else {
                    $('#value-addon').text('$');
                    $('#max-discount-row').hide();
                }
            }

            function generateCouponCode() {
                $.ajax({
                    url: '{{ route('admin.coupons.generate-code') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#code').val(response.code);
                    },
                    error: function(xhr) {
                        showToast('Không thể tạo mã giảm giá. Vui lòng thử lại.', 'error');
                    }
                });
            }

            function searchProducts() {
                productSearchTerm = $('#product-search').val();
                productPage = 1;
                $('#products-list').empty();
                showProductLoading();
                loadProducts();
            }

            function loadMoreProducts() {
                productPage++;
                loadProducts();
            }

            function handleProductScroll(container) {
                if (hasMoreProducts && !isProductLoading &&
                    container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 100) {
                    loadMoreProducts();
                }
            }

            function loadProducts() {
                if (isProductLoading) return;

                isProductLoading = true;
                $('#no-products-found').addClass('d-none');
                showProductLoading();

                $.ajax({
                    url: '{{ route('admin.coupons.load-products') }}',
                    data: {
                        page: productPage,
                        search: productSearchTerm
                    },
                    type: 'GET',
                    success: function(response) {
                        hideProductLoading();
                        isProductLoading = false;

                        if (response.total === 0) {
                            showNoProductsFound();
                            return;
                        }

                        appendProductsToList(response);

                        if (response.hasMore) {
                            $('#load-more-products').removeClass('d-none');
                        } else {
                            $('#load-more-products').addClass('d-none');
                        }

                        hasMoreProducts = response.hasMore;
                        initializeNewProductCheckboxes();

                        // Mark already selected variants if any
                        markSelectedVariants();
                    },
                    error: function(xhr) {
                        hideProductLoading();
                        isProductLoading = false;
                        showToast('Không thể tải sản phẩm. Vui lòng thử lại.', 'error');
                    }
                });
            }

            function appendProductsToList(response) {
                $('#products-list').append(response.html);
            }

            function showNoProductsFound() {
                $('#no-products-found').removeClass('d-none');
                $('#load-more-products').addClass('d-none');
            }

            function showProductLoading() {
                $('#products-loading').removeClass('d-none');
            }

            function hideProductLoading() {
                $('#products-loading').addClass('d-none');
            }

            function markSelectedVariants() {
                if (selectedVariantIds.length > 0) {
                    selectedVariantIds.forEach(id => {
                        $(`#variant_${id}`).prop('checked', true).trigger('change');
                    });
                }
            }

            function initializeNewProductCheckboxes() {
                // Product checkboxes
                $('.product-checkbox:not(.initialized)').each(function() {
                    $(this).addClass('initialized');
                    $(this).change(function() {
                        const productId = $(this).data('product-id');
                        const isChecked = $(this).prop('checked');

                        $(`#variants_${productId} .variant-checkbox`).prop('checked', isChecked)
                            .each(function() {
                                const variantId = parseInt($(this).val());
                                if (isChecked) {
                                    // Add to selected if not already in array
                                    if (!selectedVariantIds.includes(variantId)) {
                                        selectedVariantIds.push(variantId);
                                    }
                                } else {
                                    // Remove from selected
                                    selectedVariantIds = selectedVariantIds.filter(id => id !==
                                        variantId);
                                }
                            });
                    });
                });

                // Variant checkboxes
                $('.variant-checkbox:not(.initialized)').each(function() {
                    $(this).addClass('initialized');
                    $(this).change(function() {
                        const variantId = parseInt($(this).val());
                        const productId = $(this).data('product-id');
                        const isChecked = $(this).prop('checked');

                        // Update selectedVariantIds array
                        if (isChecked && !selectedVariantIds.includes(variantId)) {
                            selectedVariantIds.push(variantId);
                        } else if (!isChecked) {
                            selectedVariantIds = selectedVariantIds.filter(id => id !== variantId);
                        }

                        // Update parent product checkbox state
                        updateProductCheckbox(productId);
                    });
                });
            }

            function updateProductCheckbox(productId) {
                const totalVariants = $(`#variants_${productId} .variant-checkbox`).length;
                const checkedVariants = $(`#variants_${productId} .variant-checkbox:checked`).length;

                if (checkedVariants === 0) {
                    $(`#product_${productId}`).prop('checked', false).prop('indeterminate', false);
                } else if (checkedVariants === totalVariants) {
                    $(`#product_${productId}`).prop('checked', true).prop('indeterminate', false);
                } else {
                    $(`#product_${productId}`).prop('checked', false).prop('indeterminate', true);
                }
            }

            // --- USERS SECTION ---
            function initializeUsersSection() {
                loadInitialUsers();
            }

            function loadInitialUsers() {
                isUserLoading = true;
                $('#no-users-found').addClass('d-none');
                showUserLoading();

                $.ajax({
                    url: '{{ route('admin.coupons.initial-users') }}',
                    type: 'GET',
                    success: function(response) {
                        hideUserLoading();
                        isUserLoading = false;

                        if (response.total === 0) {
                            showNoUsersFound();
                            return;
                        }

                        $('#users-list').html(response.html);

                        // Lưu lại ID của các user đã load
                        response.users.forEach(user => {
                            if (!loadedUserIds.includes(user.id)) {
                                loadedUserIds.push(user.id);
                            }
                        });

                        // Hiển thị nút load more nếu còn user chưa load
                        if (loadedUserIds.length < response.total) {
                            $('#load-more-users').removeClass('d-none');
                            hasMoreUsers = true;
                        } else {
                            $('#load-more-users').addClass('d-none');
                            hasMoreUsers = false;
                        }

                        // Khởi tạo sự kiện cho user checkboxes
                        initializeUserCheckboxes();
                    },
                    error: function(xhr) {
                        hideUserLoading();
                        isUserLoading = false;
                        showToast('Không thể tải danh sách người dùng. Vui lòng thử lại.', 'error');
                    }
                });
            }

            function searchUsers() {
                userSearchTerm = $('#user-search').val();
                userPage = 1;
                $('#users-list').empty();
                loadedUserIds = [];
                showUserLoading();
                loadUsers();
            }

            function loadMoreUsers() {
                userPage++;
                loadUsers();
            }

            function handleUserScroll(container) {
                if (hasMoreUsers && !isUserLoading &&
                    container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 100) {
                    loadMoreUsers();
                }
            }

            function loadUsers() {
                if (isUserLoading) return;

                isUserLoading = true;
                $('#no-users-found').addClass('d-none');
                showUserLoading();

                $.ajax({
                    url: '{{ route('admin.coupons.load-users') }}',
                    data: {
                        page: userPage,
                        search: userSearchTerm,
                        format: 'html',
                        selected_user_ids: selectedUserIds,
                        exclude_ids: loadedUserIds
                    },
                    type: 'GET',
                    success: function(response) {
                        hideUserLoading();
                        isUserLoading = false;

                        if (userPage === 1 && response.total === 0) {
                            showNoUsersFound();
                            return;
                        }

                        $('#users-list').append(response.html);

                        // Cập nhật danh sách ID đã tải
                        $(response.html).find('.user-checkbox').each(function() {
                            const userId = parseInt($(this).val());
                            if (!loadedUserIds.includes(userId)) {
                                loadedUserIds.push(userId);
                            }
                        });

                        if (response.hasMore) {
                            $('#load-more-users').removeClass('d-none');
                        } else {
                            $('#load-more-users').addClass('d-none');
                        }

                        hasMoreUsers = response.hasMore;
                        initializeUserCheckboxes();

                        // Đánh dấu các user đã chọn
                        markSelectedUsers();
                    },
                    error: function(xhr) {
                        hideUserLoading();
                        isUserLoading = false;
                        showToast('Không thể tải người dùng. Vui lòng thử lại.', 'error');
                    }
                });
            }

            function showUserLoading() {
                $('#users-loading').removeClass('d-none');
            }

            function hideUserLoading() {
                $('#users-loading').addClass('d-none');
            }

            function showNoUsersFound() {
                $('#no-users-found').removeClass('d-none');
                $('#load-more-users').addClass('d-none');
            }

            function markSelectedUsers() {
                if (selectedUserIds.length > 0) {
                    selectedUserIds.forEach(id => {
                        $(`#user_${id}`).prop('checked', true);
                    });
                }
            }

            function initializeUserCheckboxes() {
                $('.user-checkbox:not(.initialized)').each(function() {
                    $(this).addClass('initialized');
                    $(this).change(function() {
                        const userId = parseInt($(this).val());
                        const isChecked = $(this).prop('checked');

                        if (isChecked) {
                            if (!selectedUserIds.includes(userId)) {
                                selectedUserIds.push(userId);
                            }

                            updateSelectedUserCount();
                        } else {
                            selectedUserIds = selectedUserIds.filter(id => id !== userId);
                            updateSelectedUserCount();
                        }
                    });
                });
            }

            function updateSelectedUserCount() {
                const count = selectedUserIds.length;
                if (count > 0) {
                    $('#selected-users-count').text(count).parent().removeClass('d-none');
                } else {
                    $('#selected-users-count').parent().addClass('d-none');
                }
            }

            // --- FORM SUBMISSION ---
            function submitForm() {
                // Clear previous errors
                $('.error-message').empty();
                $('.is-invalid').removeClass('is-invalid');

                const formData = new FormData(document.getElementById('coupon-form'));
                const submitBtn = $('.save-button');
                const originalBtnText = submitBtn.html();

                // Disable button and show loading state
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $('#coupon-form').attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        resetSubmitButton(submitBtn, originalBtnText);

                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Mã giảm giá đã được tạo thành công',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect ||
                                "{{ route('admin.coupons.index') }}";
                        });
                    },
                    error: function(xhr) {
                        resetSubmitButton(submitBtn, originalBtnText);

                        if (xhr.status === 422) {
                            handleValidationErrors(xhr.responseJSON.errors);
                        } else {
                            showToast(xhr.responseJSON?.message ||
                                'Có lỗi xảy ra, vui lòng thử lại sau.', 'error');
                        }
                    }
                });
            }

            function resetSubmitButton(button, originalText) {
                button.html(originalText);
                button.prop('disabled', false);
            }

            function handleValidationErrors(errors) {
                $.each(errors, function(field, messages) {
                    const fieldElement = $(`[name="${field}"]`);
                    const errorElement = $(`#${field}-error`);

                    fieldElement.addClass('is-invalid');
                    errorElement.text(messages[0]);
                });

                // Scroll to first error
                const firstErrorField = $('.is-invalid:first');
                if (firstErrorField.length) {
                    $('html, body').animate({
                        scrollTop: firstErrorField.offset().top - 100
                    }, 500);
                }
            }
        });
    </script>
@endpush

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Form Layout */
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .form-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: #495057;
            position: relative;
            padding-left: 15px;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 18px;
            background-color: var(--primary-color, #D1A66E);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }

        .required-asterisk {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .back-button {
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .back-button:hover {
            background-color: #e9ecef;
            color: #212529;
        }

        .save-button {
            padding: 0.5rem 1.5rem;
            background-color: var(--primary-color, #D1A66E);
            color: #fff;
            border: none;
            border-radius: 0.25rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }

        .save-button:hover {
            opacity: 0.9;
        }

        .save-button i,
        .back-button i {
            margin-right: 0.5rem;
        }

        /* Product Selection */
        .products-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        .products-container::-webkit-scrollbar {
            width: 6px;
        }

        .products-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .products-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .products-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .product-item {
            background-color: #fff;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .product-header {
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        .product-name {
            font-weight: 500;
        }

        .product-sku {
            font-size: 0.85em;
            margin-left: 5px;
            color: #6c757d;
        }

        .variant-item {
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 3px;
            padding: 8px;
            margin-bottom: 5px;
            transition: background-color 0.2s;
        }

        .variant-item:hover {
            background-color: #f9f9f9;
        }

        .variant-color {
            display: inline-block;
            margin-right: 10px;
            font-weight: 500;
        }

        .variant-size {
            margin-right: 10px;
            color: #666;
        }

        .variant-price {
            color: var(--primary-color, #D1A66E);
            font-weight: 500;
        }

        /* Select2 customization */
        .select2-container--default .select2-selection--multiple {
            border-color: #ddd;
            border-radius: 4px;
            min-height: 38px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary-color, #D1A66E);
            color: white;
            border: none;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #f8f9fa;
        }

        /* Loading and empty states */
        .products-loading {
            background-color: rgba(255, 255, 255, 0.8);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .loading-text {
            margin-top: 10px;
            color: #666;
        }

        .no-products-message {
            padding: 20px;
            text-align: center;
            color: #666;
        }

        .empty-icon {
            font-size: 36px;
            color: #ddd;
            margin-bottom: 10px;
        }

        /* Form validation */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .back-button,
            .save-button {
                width: 100%;
                justify-content: center;
            }

            .products-container {
                max-height: 300px;
            }

            .variant-item {
                width: 50%;
            }
        }

        .variant-sku {
            display: block;
            font-size: 0.75rem;
            color: #6c757d;
        }
    </style>
@endpush
