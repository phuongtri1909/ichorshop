@extends('client.layouts.information')

@section('info_title', 'My Cart - ' . request()->getHost())
@section('info_description', 'View and manage items in your shopping cart')
@section('info_keyword', 'Shopping Cart, My Cart, ' . request()->getHost())
@section('info_section_title', 'My Shopping Cart')
@section('info_section_desc', 'Review and manage items in your shopping cart')

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'My Cart',
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'My Account', 'url' => route('user.my.account')],
            ['title' => 'My Cart', 'url' => route('user.cart.index')],
        ],
    ])
@endpush

@section('info_content')
    <div class="cart-wrapper">
        @if ($cart->is_empty)
            <div class="empty-cart text-center py-5">
                <div class="empty-cart-icon mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                </div>
                <h4>Your cart is currently empty</h4>
                <p class="mb-4">Continue shopping to add items to your cart</p>
                <a href="{{ route('home') }}" class="btn btn-pry">Continue Shopping</a>
            </div>
        @else
            <div class="cart-item-count mb-3 d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $cart->total_items }}</strong> item(s) in your cart
                </div>
                <div class="select-all-container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllItems">
                        <label class="form-check-label" for="selectAllItems">
                            Select All Items
                        </label>
                    </div>
                </div>
            </div>

            <div class="cart-actions d-flex justify-content-between my-2">
                <button type="button" id="clearCartBtn" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-2"></i>All
                </button>
                <button type="button" id="removeSelectedBtn" class="btn btn-outline-danger" disabled>
                    <i class="fas fa-trash me-2"></i>Selected
                </button>
            </div>

            <div class="cart-header d-none d-md-flex pb-2 mb-3">
                <div class="row w-100">
                    <div class="col-md-6">Product</div>
                    <div class="col-md-2 text-center">Price</div>
                    <div class="col-md-2 text-center">Quantity</div>
                    <div class="col-md-2 text-end">Total</div>
                </div>
            </div>

            <div class="cart-items mb-5 pb-5">
                @php
                    $currentProductId = null;
                    $isFirstVariant = true;
                @endphp
                
                @foreach ($cart->items as $item)
                    @php
                        $isNewProduct = $currentProductId !== $item->product_id;

                        if ($isNewProduct) {
                            $currentProductId = $item->product_id;
                            $isFirstVariant = true;
                        }
                    @endphp

                    <div class="cart-item py-3 border-bottom {{ $isNewProduct ? 'product-group-start' : '' }} {{ $isFirstVariant ? '' : 'variant-item' }}" 
                         data-item-id="{{ $item->id }}" 
                         data-product-id="{{ $item->product_id }}">
                        <div class="row align-items-center">
                           
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="cart-item-checkbox me-2">
                                        <div class="form-check">
                                            <input class="form-check-input item-checkbox" type="checkbox" 
                                                value="{{ $item->id }}" 
                                                id="cartItem{{ $item->id }}" 
                                                {{ $item->is_checkable ? '' : 'disabled' }}
                                                data-price="{{ $item->price }}"
                                                data-quantity="{{ $item->quantity }}">
                                        </div>
                                    </div>
                                    <div class="cart-item-image">
                                        <a href="{{ route('product.details', $item->product->slug) }}">
                                            <img src="{{ $item->product->avatar_url }}" alt="{{ $item->product->name }}"
                                                class="img-fluid rounded"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div class="cart-item-info ms-3">
                                        <h5 class="cart-item-title mb-1">
                                            <a href="{{ route('product.details', $item->product->slug) }}"
                                                class="text-decoration-none text-dark">
                                                {{ $item->product->name }}
                                            </a>
                                        </h5>
                                        <div class="cart-item-variant text-muted small">
                                            @if ($item->variant->color_name)
                                                <span class="me-2">Color: {{ $item->variant->color_name }}</span>
                                            @endif
                                            @if ($item->variant->size)
                                                <span>Size: {{ $item->variant->size }}</span>
                                            @endif

                                            @if (!$item->is_checkable)
                                                <span class="badge bg-danger ms-2">Out of stock</span>
                                            @elseif ($item->variant->quantity < 5)
                                                <span class="badge bg-warning text-dark ms-2">Only {{ $item->variant->quantity }} left</span>
                                            @endif
                                        </div>
                                        <div class="d-md-none d-flex justify-content-between align-items-center mt-2">
                                            <div class="cart-item-price">
                                                <span
                                                    class="text-danger fw-bold">${{ number_format($item->price, 2) }}</span>
                                            </div>
                                            <button type="button" class="btn-remove-item btn btn-sm text-danger border-0"
                                                data-item-id="{{ $item->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 text-center d-none d-md-block">
                                <div class="cart-item-price">
                                    @if ($item->promotion_info)
                                        <span class="text-danger fw-bold">${{ number_format($item->price, 2) }}</span>
                                        <div class="original-price text-decoration-line-through text-muted small">
                                            ${{ number_format($item->promotion_info['original_price'], 2) }}
                                        </div>
                                    @else
                                        <span class="fw-bold">${{ number_format($item->price, 2) }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-2 text-center">
                                <div class="quantity-selector d-inline-flex">
                                    <button class="btn-decrease btn btn-sm border" data-item-id="{{ $item->id }}" 
                                            {{ !$item->is_checkable ? 'disabled' : '' }}>
                                        <i class="fas fa-minus small"></i>
                                    </button>
                                    <input type="number" class="rounded-1 border text-center quantity-input"
                                        value="{{ $item->quantity }}" min="1" max="{{ $item->variant->quantity }}"
                                        data-item-id="{{ $item->id }}" style="width: 50px; border-radius: 0;"
                                        {{ !$item->is_checkable ? 'disabled' : '' }}>
                                    <button class="btn-increase btn btn-sm border" data-item-id="{{ $item->id }}"
                                            {{ !$item->is_checkable ? 'disabled' : '' }}>
                                        <i class="fas fa-plus small"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-2 text-end d-none d-md-block">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="subtotal fw-bold">${{ number_format($item->subtotal, 2) }}</span>
                                    <button type="button" class="btn-remove-item btn btn-sm text-danger border-0"
                                        data-item-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $isFirstVariant = false;
                    @endphp
                @endforeach
            </div>

            <!-- Thanh tổng tiền cố định ở bottom -->
            <div class="fixed-checkout-bar">
                <div class="checkout-bar-container">
                    <div class="checkout-bar-content">
                        <div class="checkout-bar-toggle">
                            <button class="btn btn-sm btn-outline-secondary toggle-checkout-details" type="button">
                                <i class="fas fa-chevron-up"></i>
                            </button>
                        </div>
                        
                        <div class="checkout-bar-expanded">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Items Selected:</span>
                                        <strong class="badge bg-primary" id="mobileSelectedItemsCount">0</strong>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <div class="d-flex justify-content-md-center">
                                        <span class="me-2">Total:</span>
                                        <strong class="text-danger" id="mobileCheckoutTotal">$0.00</strong>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="button" id="mobileCheckoutBtn" class="btn btn-pry w-100" disabled>
                                        <i class="fas fa-credit-card me-2"></i>Checkout
                                    </button>
                                </div>
                            </div>
                            
                            <div class="checkout-details mt-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Items (<span id="detailSelectedItemsCount">0</span>):</span>
                                            <span class="fw-bold" id="detailSelectedItemsTotal">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Discount:</span>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Shipping:</span>
                                            <span>$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                            <span class="fw-bold">Total:</span>
                                            <span class="fw-bold text-danger" id="detailCheckoutTotal">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="checkout-bar-collapsed">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Total:</span>
                                        <strong class="text-danger" id="collapsedCheckoutTotal">$0.00</strong>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" id="collapsedCheckoutBtn" class="btn btn-pry" disabled>
                                        <i class="fas fa-credit-card me-2"></i>Checkout (<span id="collapsedSelectedItemsCount">0</span>)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            let selectedItems = [];
            let isCheckoutExpanded = true;
            
            // Toggle thanh tổng tiền
            $('.toggle-checkout-details').on('click', function() {
                isCheckoutExpanded = !isCheckoutExpanded;
                updateCheckoutBarState();
            });
            
            function updateCheckoutBarState() {
                if (isCheckoutExpanded) {
                    $('.checkout-bar-expanded').slideDown(300);
                    $('.checkout-bar-collapsed').slideUp(300);
                    $('.toggle-checkout-details i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    $('.checkout-bar-expanded').slideUp(300);
                    $('.checkout-bar-collapsed').slideDown(300);
                    $('.toggle-checkout-details i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
                
                // Lưu trạng thái vào localStorage
                localStorage.setItem('checkoutBarExpanded', isCheckoutExpanded);
            }

            // Khôi phục trạng thái từ localStorage
            if (localStorage.getItem('checkoutBarExpanded') !== null) {
                isCheckoutExpanded = localStorage.getItem('checkoutBarExpanded') === 'true';
                updateCheckoutBarState();
            } else {
                // Mặc định hiển thị dạng thu gọn trên mobile
                if (window.innerWidth < 768) {
                    isCheckoutExpanded = false;
                    updateCheckoutBarState();
                }
            }
            
            // Xử lý nút checkout ở thanh thu gọn
            $('#collapsedCheckoutBtn').on('click', function() {
                processCheckout();
            });
            
            // Xử lý nút checkout ở thanh mở rộng
            $('#mobileCheckoutBtn').on('click', function() {
                processCheckout();
            });
            
            function processCheckout() {
                if (selectedItems.length === 0) {
                    showToast('Please select at least one item to checkout.', 'warning');
                    return;
                }
                
                // Chuyển đến trang thanh toán với danh sách các sản phẩm đã chọn
                // Ở đây, bạn có thể thay thế bằng URL thật của trang thanh toán
                const checkoutUrl = "{{ route('home') }}"; // Thay thế bằng route checkout thực tế
                
                // Tạo form ẩn để submit danh sách item đã chọn
                const $form = $('<form>', {
                    action: checkoutUrl,
                    method: 'POST',
                    style: 'display: none;'
                });
                
                // Thêm CSRF token
                $form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                
                // Thêm các item đã chọn
                selectedItems.forEach(function(itemId) {
                    $form.append($('<input>', {
                        type: 'hidden',
                        name: 'items[]',
                        value: itemId
                    }));
                });
                
                // Thêm form vào body và submit
                $('body').append($form);
                $form.submit();
            }

            $('.quantity-input').on('change', function() {
                const itemId = $(this).data('item-id');
                const quantity = parseInt($(this).val());
                const maxQuantity = parseInt($(this).attr('max'));

                // Kiểm tra giá trị hợp lệ
                if (quantity < 1) {
                    $(this).val(1);
                    updateCartItem(itemId, 1);
                    return;
                }

                if (quantity > maxQuantity) {
                    $(this).val(maxQuantity);
                    showToast(`Only ${maxQuantity} items left in stock`, 'warning');
                    updateCartItem(itemId, maxQuantity);
                    return;
                }

                updateCartItem(itemId, quantity);
            });

            // Nút tăng số lượng
            $('.btn-increase').on('click', function() {
                const itemId = $(this).data('item-id');
                const $input = $(`.quantity-input[data-item-id="${itemId}"]`);
                const currentValue = parseInt($input.val());
                const maxValue = parseInt($input.attr('max'));

                if (currentValue < maxValue) {
                    $input.val(currentValue + 1);
                    updateCartItem(itemId, currentValue + 1);
                } else {
                    showToast(`Only ${maxValue} items left in stock`, 'warning');
                }
            });

            // Nút giảm số lượng
            $('.btn-decrease').on('click', function() {
                const itemId = $(this).data('item-id');
                const $input = $(`.quantity-input[data-item-id="${itemId}"]`);
                const currentValue = parseInt($input.val());

                if (currentValue > 1) {
                    $input.val(currentValue - 1);
                    updateCartItem(itemId, currentValue - 1);
                }
            });

            // Xóa sản phẩm
            $('.btn-remove-item').on('click', function() {
                const itemId = $(this).data('item-id');
                const $item = $(`.cart-item[data-item-id="${itemId}"]`);
                const productName = $item.find('.cart-item-title a').text().trim();

                Swal.fire({
                    title: 'Remove Item?',
                    text: `Do you want to remove "${productName}" from your cart?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeCartItem(itemId);
                    }
                });
            });

            // Xóa toàn bộ giỏ hàng
            $('#clearCartBtn').on('click', function() {
                Swal.fire({
                    title: 'Clear Cart?',
                    text: 'Are you sure you want to remove all items from your cart?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, clear cart!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearCart();
                    }
                });
            });
            
            // Xử lý checkbox chọn tất cả
            $('#selectAllItems').on('change', function() {
                const isChecked = $(this).prop('checked');
                
                // Chỉ chọn các item có thể chọn được (không bị disabled)
                $('.item-checkbox:not(:disabled)').prop('checked', isChecked);
                
                // Cập nhật danh sách item đã chọn
                updateSelectedItems();
            });
            
            // Xử lý checkbox sản phẩm riêng lẻ
            $('.item-checkbox').on('change', function() {
                updateSelectedItems();
                
                // Kiểm tra xem tất cả checkbox có được chọn không
                const allCheckable = $('.item-checkbox:not(:disabled)').length;
                const allChecked = $('.item-checkbox:checked').length;
                
                $('#selectAllItems').prop('checked', allChecked > 0 && allChecked === allCheckable);
            });
            
            // Xử lý nút xóa các sản phẩm đã chọn
            $('#removeSelectedBtn').on('click', function() {
                if (selectedItems.length === 0) return;
                
                Swal.fire({
                    title: 'Remove Selected Items?',
                    text: 'Are you sure you want to remove all selected items from your cart?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove them!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeSelectedItems();
                    }
                });
            });
            
            // Xử lý nút thanh toán
            $('#checkoutBtn').on('click', function() {
                processCheckout();
            });
            
            // Cập nhật danh sách item đã chọn
            function updateSelectedItems() {
                selectedItems = [];
                let totalPrice = 0;
                let totalCount = 0;
                
                $('.item-checkbox:checked').each(function() {
                    const itemId = $(this).val();
                    const price = parseFloat($(this).data('price'));
                    const quantity = parseInt($(this).data('quantity'));
                    
                    selectedItems.push(itemId);
                    totalPrice += price * quantity;
                    totalCount += quantity;
                });
                
                // Cập nhật UI
                $('#selectedItemsCount').text(totalCount);
                $('#selectedItemsTotal').text('$' + formatNumber(totalPrice));
                $('#checkoutTotal').text('$' + formatNumber(totalPrice));
                
                // Cập nhật UI cho thanh checkout cố định
                $('#mobileSelectedItemsCount').text(totalCount);
                $('#mobileCheckoutTotal').text('$' + formatNumber(totalPrice));
                $('#detailSelectedItemsCount').text(totalCount);
                $('#detailSelectedItemsTotal').text('$' + formatNumber(totalPrice));
                $('#detailCheckoutTotal').text('$' + formatNumber(totalPrice));
                $('#collapsedCheckoutTotal').text('$' + formatNumber(totalPrice));
                $('#collapsedSelectedItemsCount').text(totalCount);
                
                // Enable/disable các nút
                const isAnySelected = selectedItems.length > 0;
                $('#checkoutBtn').prop('disabled', !isAnySelected);
                $('#mobileCheckoutBtn').prop('disabled', !isAnySelected);
                $('#collapsedCheckoutBtn').prop('disabled', !isAnySelected);
                $('#removeSelectedBtn').prop('disabled', !isAnySelected);
            }
            
            // Xóa các item đã chọn
            function removeSelectedItems() {
                if (selectedItems.length === 0) return;
                
                // Hiển thị loading
                Swal.fire({
                    title: 'Removing items...',
                    html: 'Please wait while we remove the selected items.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Tạo một promises array để theo dõi tất cả các request xóa
                const deletePromises = selectedItems.map(itemId => {
                    return new Promise((resolve, reject) => {
                        const removeUrl = "{{ route('user.cart.remove', ':id') }}".replace(':id', itemId);
                        $.ajax({
                            url: removeUrl,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    resolve();
                                } else {
                                    reject(response.error || 'Cannot delete product');
                                }
                            },
                            error: function() {
                                reject('An error occurred while deleting the product.');
                            }
                        });
                    });
                });
                
                // Xử lý tất cả các promises
                Promise.all(deletePromises)
                    .then(() => {
                        // Tất cả các item đã được xóa thành công
                        showToast('Selected items removed successfully.', 'success');
                        
                        // Tải lại trang sau khi xóa thành công
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    })
                    .catch(error => {
                        showToast(error, 'error');
                    });
            }
            
            // Hàm cập nhật số lượng sản phẩm
            function updateCartItem(itemId, quantity) {
                const updateUrl = "{{ route('user.cart.update', ':id') }}".replace(':id', itemId);
                $.ajax({
                    url: updateUrl,
                    type: 'POST',
                    data: {
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const $item = $(`.cart-item[data-item-id="${itemId}"]`);
                            $item.find('.subtotal').text('$' + formatNumber(response.item.subtotal));

                            // Cập nhật data-quantity trong checkbox
                            $(`#cartItem${itemId}`).data('quantity', quantity);

                            // Cập nhật tổng giá trị giỏ hàng
                            updateSelectedItems();

                            // Cập nhật số lượng trên header
                            $('#cartCount').text(response.cart.total_items);
                        } else {
                            showToast(response.error || 'Cannot update cart', 'error');
                        }
                    },
                    error: function() {
                        showToast('An error occurred while updating the cart.', 'error');
                    }
                });
            }

            function removeCartItem(itemId) {
                const removeUrl = "{{ route('user.cart.remove', ':id') }}".replace(':id', itemId);
                $.ajax({
                    url: removeUrl,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const $item = $(`.cart-item[data-item-id="${itemId}"]`);
                            const productId = $item.data('product-id');
                            
                            $item.fadeOut(300, function() {
                                // Xóa item hiện tại
                                $(this).remove();
                                
                                // Kiểm tra xem còn item nào của sản phẩm này không
                                const $remainingItems = $(`.cart-item[data-product-id="${productId}"]`);
                                
                                if ($remainingItems.length > 0) {
                                    // Nếu còn item, đặt item đầu tiên làm item bắt đầu nhóm
                                    $remainingItems.first().addClass('product-group-start');
                                }
                                
                                // Kiểm tra xem còn sản phẩm nào không
                                if ($('.cart-item').length === 0) {
                                    location.reload(); // Tải lại trang để hiển thị giỏ hàng trống
                                }
                            });
                            
                            // Cập nhật tổng tiền
                            updateSelectedItems();
                            
                            // Cập nhật số lượng trên header
                            $('#cartCount').text(response.cart.total_items);
                        } else {
                            showToast(response.error || 'Cannot delete product', 'error');
                        }
                    },
                    error: function() {
                        showToast('An error occurred while deleting the product.', 'error');
                    }
                });
            }

            function clearCart() {
                $.ajax({
                    url: '{{ route('user.cart.clear') }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload(); 
                        } else {
                            showToast(response.error || 'Cannot delete product', 'error');
                        }
                    },
                    error: function() {
                        showToast('An error occurred while deleting the cart.', 'error');
                    }
                });
            }

            function formatNumber(number) {
                return parseFloat(number).toFixed(2);
            }
            
            // Toast function
            function showToast(message, type) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
            
            // Khởi tạo
            updateSelectedItems();
        });
    </script>
@endpush

@push('styles')
    <style>
        .cart-wrapper {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            position: relative;
            padding-bottom: 100px; /* Tạo không gian cho thanh checkout cố định */
        }

        .cart-item-image img {
            transition: transform 0.3s;
        }

        .cart-item-image:hover img {
            transform: scale(1.05);
        }

        .cart-item-title a:hover {
            color: var(--primary-color) !important;
        }

        .empty-cart-icon {
            opacity: 0.5;
        }

        .btn-remove-item {
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .btn-remove-item:hover {
            opacity: 1;
        }

        .quantity-selector {
            border-radius: 4px;
            overflow: hidden;
        }

        .quantity-selector .btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .quantity-input {
            -moz-appearance: textfield;
        }

        .cart-summary .card {
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .cart-summary .card:hover {
            transform: translateY(-5px);
        }
        
        /* Định dạng nhóm sản phẩm */
        .product-group-start {
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
        }
        
        .variant-item {
            background-color: #fafafa;
            padding-left: 10px;
        }
        
        /* Định dạng checkbox */
        .cart-item-checkbox .form-check {
            padding-left: 0;
        }
        
        .cart-item-checkbox .form-check-input {
            cursor: pointer;
            width: 20px;
            height: 20px;
        }
        
        .cart-item-checkbox .form-check-input:disabled {
            opacity: 0.5;
            background-color: #f8f8f8;
        }
        
        .select-all-container {
            margin-right: 15px;
        }
        
        .select-all-container .form-check-input {
            cursor: pointer;
            width: 18px;
            height: 18px;
            margin-top: 0.25rem;
        }
        
        .select-all-container .form-check-label {
            font-weight: 500;
            cursor: pointer;
        }

        /* Thanh checkout cố định */
        .fixed-checkout-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        
        .checkout-bar-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .checkout-bar-content {
            position: relative;
            padding: 15px 0;
        }
        
        .checkout-bar-toggle {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .checkout-bar-toggle .btn {
            width: 36px;
            height: 42px;
            border-radius: 50%;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .checkout-bar-expanded {
            display: block;
        }
        
        .checkout-bar-collapsed {
            display: none;
        }
        
        .checkout-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .checkout-bar-expanded.expanded .checkout-details {
            max-height: 200px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .cart-item {
                padding: 15px 0;
            }
            
            .variant-item {
                padding-left: 5px;
            }
            
            .checkout-bar-container {
                padding: 0 10px;
            }
        }
    </style>
@endpush
