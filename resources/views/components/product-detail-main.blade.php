<section class="product-detail-section pb-5">
    <div class="container">
        <div class="row h-100">
            <!-- Product Images -->
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="product-images w-100 d-flex gap-3">
                    <!-- Thumbnails -->
                    <div class="product-thumbnails d-flex flex-column gap-3">
                        @php
                            $totalImages = count($product['images']);
                        @endphp

                        <!-- Always maintain 3-box structure but only show actual images -->
                        @for ($i = 0; $i < 3; $i++)
                            @if ($i < $totalImages && $totalImages <= 3)
                                <!-- Show actual image if available and total is 3 or less -->
                                <div class="thumbnail-item {{ $i === 0 ? 'active' : '' }} flex-1"
                                    data-index="{{ $i }}">
                                    <img src="{{ asset($product['images'][$i]) }}" alt="Product {{ $i + 1 }}"
                                        class="img-fluid">
                                </div>
                            @elseif ($i < 2 && $totalImages > 3)
                                <!-- For 4+ images: Show first 2 images -->
                                <div class="thumbnail-item {{ $i === 0 ? 'active' : '' }} flex-1"
                                    data-index="{{ $i }}">
                                    <img src="{{ asset($product['images'][$i]) }}" alt="Product {{ $i + 1 }}"
                                        class="img-fluid">
                                </div>
                            @elseif ($i === 2 && $totalImages > 3)
                                <!-- For 4+ images: Show more indicator on 3rd slot -->
                                <div class="thumbnail-item more-images flex-1" data-toggle="modal"
                                    data-target="#imageGalleryModal">
                                    <img src="{{ asset($product['images'][2]) }}" alt="More images" class="img-fluid">
                                    <div class="more-overlay">
                                        <span>+{{ $totalImages - 2 }}</span>
                                    </div>
                                </div>
                            @else
                                <!-- Empty placeholder - invisible but maintains spacing -->
                                <div class="thumbnail-item empty flex-1"></div>
                            @endif
                        @endfor
                    </div>

                    <!-- Main Image -->
                    <div class="product-main-image rounded-4 flex-1">
                        <div class="main-image-container position-relative">
                            <img src="{{ asset($product['images'][0]) }}" alt="{{ $product['name'] }}"
                                class="img-fluid main-image w-100 h-100 rounded-4" data-current-index="0">

                            <!-- Navigation arrows for main image -->
                            @if ($totalImages > 1)
                                <button class="image-nav prev-btn" type="button">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="image-nav next-btn" type="button">
                                    <i class="fas fa-chevron-right"></i>
                                </button>

                                <!-- Image indicators -->
                                <div class="image-indicators">
                                    @foreach ($product['images'] as $index => $image)
                                        <span class="indicator {{ $index === 0 ? 'active' : '' }}"
                                            data-index="{{ $index }}"></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="product-info w-100 d-flex flex-column justify-content-between">
                    <div>
                        <h2 class="product-title">{{ $product['name'] }}</h2>

                        <!-- Rating -->
                        <div class="product-rating mb-3">
                            <div class="rating-stars fs-5">
                                @php
                                    $rating = $product['rating'];
                                    $fullStars = floor($rating);
                                    $hasHalfStar = $rating - $fullStars >= 0.5;
                                @endphp

                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $fullStars)
                                        <i class="fas fa-star cl-ffe371"></i>
                                    @elseif($i == $fullStars + 1 && $hasHalfStar)
                                        <i class="fas fa-star-half-alt cl-ffe371"></i>
                                    @else
                                        <i class="far fa-star cl-ffe371"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-text">{{ $product['rating'] }}/5</span>
                            <span onclick="event.preventDefault()">
                                <x-wishlist-button :product="$product" class="color-primary" />
                            </span>
                        </div>

                        <!-- Price -->
                        <div class="product-price mb-4">
                            <span class="current-price fw-bold">${{ $product['current_price'] }}</span>
                            @if (isset($product['original_price']))
                                <span class="original-price fw-bold">${{ $product['original_price'] }}</span>
                                <span class="discount-badge">-{{ $product['discount'] }}%</span>
                            @endif
                        </div>
                        <div class="product-stock-info">

                        </div>

                        <!-- Description -->
                        <p class="product-description color-primary-5 mb-4">{!! nl2br(e($product['description'])) !!}</p>

                    </div>

                    <div>
                        <!-- Colors -->
                        <div class="product-options mb-4">
                            <h6 class="option-title">Select Colors</h6>
                            <div class="color-options">
                                @if (isset($product['variants_without_color']) && count($product['variants_without_color']) > 0)
                                    <div class="color-option default-option {{ $product['default_color'] === null ? 'active' : '' }}"
                                        data-color="">
                                        <i class="fa-solid fa-paint-roller position-absolute"
                                            style="top: 12px;left:10px"></i>
                                    </div>
                                @endif

                                @foreach ($product['colors'] as $colorName => $colorCode)
                                    <div class="color-option {{ $colorName === $product['default_color'] ? 'active' : '' }}"
                                        data-color="{{ $colorCode }}" data-color-name="{{ $colorName }}"
                                        style="background-color: {{ $colorCode }};">
                                        <div class="color-circle"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sizes -->
                        <div class="product-options mb-4">
                            <h6 class="option-title">Choose Size</h6>
                            <div class="size-options">
                                @foreach ($product['all_sizes'] as $size)
                                    <div class="size-option {{ $size === $product['default_size'] ? 'active' : '' }}"
                                        data-size="{{ $size }}">
                                        {{ $size }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <input type="hidden" id="selected-variant-id"
                            value="{{ $product['cheapest_variant']['id'] ?? '' }}">

                        <!-- Quantity & Add to Cart -->
                        <div class="product-actions d-flex">
                            <div class="quantity-selector me-2">
                                <button class="quantity-btn minus" type="button">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" value="1" min="1" readonly>
                                <button class="quantity-btn plus" type="button">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button class="btn add-to-cart-btn">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Image Gallery Modal -->
<div class="modal fade" id="imageGalleryModal" tabindex="-1" role="dialog" aria-labelledby="imageGalleryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageGalleryModalLabel">Product Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="gallery-container">
                    <div class="gallery-main-image">
                        <img src="{{ asset($product['images'][0]) }}" alt="Product Image"
                            class="img-fluid gallery-image">
                        <button class="gallery-nav gallery-prev" type="button">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="gallery-nav gallery-next" type="button">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="gallery-thumbnails">
                        @foreach ($product['images'] as $index => $image)
                            <div class="gallery-thumb {{ $index === 0 ? 'active' : '' }}"
                                data-index="{{ $index }}">
                                <img src="{{ asset($image) }}" alt="Product {{ $index + 1 }}" class="img-fluid">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            // Định nghĩa biến toàn cục
            const assetUrl = '{{ asset('') }}';

            // Dữ liệu sản phẩm
            const productImages = @json($product['images']);
            const allVariants = @json($product['all_variants'] ?? []);
            const defaultImages = @json($product['default_images'] ?? $product['images']);
            const colorImages = @json($product['color_images'] ?? []);

            // Trạng thái hiện tại
            let currentImageIndex = 0;
            let galleryCurrentIndex = 0;
            let currentColorName = "{{ $product['default_color'] }}";
            let currentSize = "{{ $product['default_size'] }}";
            let currentVariantId = $('#selected-variant-id').val();

            // Các phần tử DOM
            const $mainImage = $('.main-image');
            const $thumbnails = $('.thumbnail-item:not(.more-images)');
            const $prevBtn = $('.prev-btn');
            const $nextBtn = $('.next-btn');
            const $indicators = $('.indicator');
            const $mainImageContainer = $('.main-image-container');
            const $moreImages = $('.thumbnail-item.more-images');

            // ------ QUẢN LÝ HÌNH ẢNH -------

            // Cập nhật hình ảnh chính
            function updateMainImage(index) {
                currentImageIndex = index;
                $mainImage.attr('src', assetUrl + productImages[index])
                    .attr('data-current-index', index);

                $indicators.removeClass('active').eq(index).addClass('active');
                $thumbnails.removeClass('active');

                $thumbnails.each(function() {
                    if ($(this).data('index') == index) {
                        $(this).addClass('active');
                    }
                });
            }

            // Lấy hình ảnh theo màu
            function getImagesForColor(colorName) {
                if (!colorName) {
                    return defaultImages; // Trả về hình ảnh mặc định nếu không có màu
                }

                return colorImages[colorName] || defaultImages;
            }

            // Cập nhật tất cả hình ảnh dựa trên màu đã chọn
            function updateProductImages(colorName) {
                const images = getImagesForColor(colorName);

                if (images && images.length > 0) {
                    // Cập nhật biến toàn cục để các hàm khác có thể truy cập
                    window.productImages = images;

                    // Cập nhật main image và indicators
                    $mainImage.attr('src', assetUrl + images[0]);
                    $mainImage.attr('data-current-index', 0);
                    currentImageIndex = 0;

                    // Cập nhật indicators
                    if ($indicators.length > 0) {
                        const $indicatorsContainer = $('.image-indicators');
                        $indicatorsContainer.empty();
                        images.forEach((_, idx) => {
                            $indicatorsContainer.append(
                                `<span class="indicator ${idx === 0 ? 'active' : ''}" data-index="${idx}"></span>`
                            );
                        });
                        // Gắn lại sự kiện cho indicators
                        $('.indicator').on('click', function() {
                            updateMainImage($(this).data('index'));
                        });
                    }

                    // Cập nhật thumbnails và gallery
                    updateThumbnails(images);
                    updateGalleryImages(images);
                }
            }

            // Cập nhật ảnh thumbnail
            function updateThumbnails(images) {
                const $thumbnailsContainer = $('.product-thumbnails');
                $thumbnailsContainer.empty();

                const totalImages = images.length;

                for (let i = 0; i < 3; i++) {
                    if (i < totalImages && totalImages <= 3) {
                        // Hiển thị ảnh thật nếu có tối đa 3 ảnh
                        $thumbnailsContainer.append(`
                            <div class="thumbnail-item ${i === 0 ? 'active' : ''} flex-1" data-index="${i}">
                                <img src="${assetUrl}${images[i]}" alt="Product ${i + 1}" class="img-fluid">
                            </div>
                        `);
                    } else if (i < 2 && totalImages > 3) {
                        // Hiển thị 2 ảnh đầu nếu có nhiều hơn 3 ảnh
                        $thumbnailsContainer.append(`
                            <div class="thumbnail-item ${i === 0 ? 'active' : ''} flex-1" data-index="${i}">
                                <img src="${assetUrl}${images[i]}" alt="Product ${i + 1}" class="img-fluid">
                            </div>
                        `);
                    } else if (i === 2 && totalImages > 3) {
                        // Hiển thị nút "Xem thêm" cho vị trí thứ 3
                        $thumbnailsContainer.append(`
                            <div class="thumbnail-item more-images flex-1" data-toggle="modal" data-target="#imageGalleryModal">
                                <img src="${assetUrl}${images[2]}" alt="More images" class="img-fluid">
                                <div class="more-overlay">
                                    <span>+${totalImages - 2}</span>
                                </div>
                            </div>
                        `);
                    } else {
                        // Ô giữ chỗ trống (ẩn)
                        $thumbnailsContainer.append(`
                            <div class="thumbnail-item empty flex-1"></div>
                        `);
                    }
                }

                // Gắn lại sự kiện click
                $('.thumbnail-item:not(.more-images)').on('click', function() {
                    const index = $(this).data('index');
                    updateMainImage(index);
                });

                $('.thumbnail-item.more-images').on('click', function() {
                    $('#imageGalleryModal').modal('show');
                });
            }

            // Cập nhật gallery trong modal
            function updateGalleryImages(images) {
                const $galleryContainer = $('.gallery-thumbnails');
                if ($galleryContainer.length) {
                    $galleryContainer.empty();

                    images.forEach((image, index) => {
                        $galleryContainer.append(`
                            <div class="gallery-thumb ${index === 0 ? 'active' : ''}" data-index="${index}">
                                <img src="${assetUrl}${image}" alt="Product ${index + 1}" class="img-fluid">
                            </div>
                        `);
                    });

                    // Đặt hình đầu tiên làm hình chính
                    $('.gallery-image').attr('src', assetUrl + images[0]);

                    // Gắn lại sự kiện click
                    $('.gallery-thumb').on('click', function() {
                        const index = $(this).data('index');
                        updateGalleryImage(index);
                    });
                }
            }

            // Cập nhật ảnh trong gallery modal
            function updateGalleryImage(index) {
                galleryCurrentIndex = index;
                const currentImages = window.productImages || productImages;
                $('.gallery-image').attr('src', assetUrl + currentImages[index]);
                $('.gallery-thumb').removeClass('active');
                $(`.gallery-thumb[data-index="${index}"]`).addClass('active');
            }

            // ------ QUẢN LÝ BIẾN THỂ SẢN PHẨM -------

            // Lấy danh sách size có sẵn cho màu đã chọn
            function getSizesForColor(colorName) {
                if (!colorName) {
                    // Với màu mặc định, tìm các biến thể không có màu
                    return allVariants
                        .filter(v => !v.color && !v.color_name)
                        .map(v => v.size)
                        .filter(Boolean);
                } else {
                    // Với màu cụ thể, tìm các biến thể khớp
                    return allVariants
                        .filter(v => v.color_name === colorName)
                        .map(v => v.size)
                        .filter(Boolean);
                }
            }

            // Lấy danh sách màu có sẵn cho size đã chọn
            function getColorsForSize(size) {
                if (!size) {
                    // Với size null
                    return allVariants
                        .filter(v => !v.size)
                        .map(v => v.color_name)
                        .filter(Boolean);
                } else {
                    // Với size cụ thể
                    return allVariants
                        .filter(v => v.size === size)
                        .map(v => v.color_name)
                        .filter(Boolean);
                }
            }

            // Cập nhật tùy chọn size dựa trên màu đã chọn
            function updateSizeOptions(colorName) {
                const availableSizes = getSizesForColor(colorName);

                $('.size-option').each(function() {
                    const size = $(this).data('size');

                    if (availableSizes.includes(size)) {
                        $(this).removeClass('disabled');
                    } else {
                        $(this).addClass('disabled');

                        // Nếu size hiện tại đang được chọn nhưng bị vô hiệu hóa, bỏ chọn nó
                        if (size === currentSize && $(this).hasClass('active')) {
                            $(this).removeClass('active');
                            currentSize = null;
                        }
                    }
                });

                // Nếu size hiện tại không hợp lệ, chọn size đầu tiên có sẵn
                if (!availableSizes.includes(currentSize) && availableSizes.length > 0) {
                    currentSize = availableSizes[0];
                    $(`.size-option[data-size="${currentSize}"]`).addClass('active');
                }

                findAndUpdateVariant(); // Chỉ cập nhật giá, không gọi updateColorOptions
            }

            // Cập nhật tùy chọn màu dựa trên size đã chọn
            function updateColorOptions(size) {
                const availableColors = getColorsForSize(size);

                $('.color-option').each(function() {
                    const colorName = $(this).data('color-name');

                    // Xử lý tùy chọn mặc định riêng
                    if ($(this).hasClass('default-option')) {
                        // Kiểm tra xem có biến thể mặc định cho size này không
                        const hasDefaultVariantsForSize = allVariants.some(v =>
                            (!v.color && !v.color_name) &&
                            (size ? v.size === size : true)
                        );

                        if (hasDefaultVariantsForSize) {
                            $(this).removeClass('disabled');
                        } else {
                            $(this).addClass('disabled');

                            // Nếu đang chọn mặc định nhưng bị vô hiệu hóa
                            if ((currentColorName === null || currentColorName === '') && $(this).hasClass(
                                    'active')) {
                                $(this).removeClass('active');
                                currentColorName = null;
                            }
                        }
                        return;
                    }

                    // Xử lý tùy chọn màu thông thường
                    if (availableColors.includes(colorName)) {
                        $(this).removeClass('disabled');
                    } else {
                        $(this).addClass('disabled');

                        // Nếu màu hiện tại đang được chọn nhưng bị vô hiệu hóa, bỏ chọn nó
                        if (colorName === currentColorName && $(this).hasClass('active')) {
                            $(this).removeClass('active');
                            currentColorName = null;
                        }
                    }
                });

                // Nếu màu hiện tại không hợp lệ, chọn màu đầu tiên có sẵn
                if (currentColorName === null || !availableColors.includes(currentColorName)) {
                    if ($('.color-option.default-option').length && !$('.color-option.default-option').hasClass(
                            'disabled')) {
                        currentColorName = '';
                        $('.color-option.default-option').addClass('active');
                    } else if (availableColors.length > 0) {
                        currentColorName = availableColors[0];
                        $(`.color-option[data-color-name="${currentColorName}"]`).addClass('active');
                    }
                }

                updateProductImages(currentColorName);
                findAndUpdateVariant(); // Chỉ cập nhật giá, không gọi updateSizeOptions
            }

            // Tìm biến thể dựa trên lựa chọn hiện tại
            function findAndUpdateVariant() {
                let matchedVariant;

                if (!currentColorName && !currentSize) {
                    // Tìm biến thể mặc định (không màu, không size)
                    matchedVariant = allVariants.find(v => !v.color_name && !v.size);
                } else if (!currentColorName) {
                    // Tìm biến thể với size đã chọn nhưng không có màu
                    matchedVariant = allVariants.find(v => !v.color_name && v.size === currentSize);
                } else if (!currentSize) {
                    // Tìm biến thể với màu đã chọn nhưng không có size
                    matchedVariant = allVariants.find(v => v.color_name === currentColorName && !v.size);
                } else {
                    // Tìm biến thể khớp chính xác
                    matchedVariant = allVariants.find(v =>
                        v.color_name === currentColorName &&
                        v.size === currentSize
                    );
                }

                if (matchedVariant) {
                    currentVariantId = matchedVariant.id;
                    $('#selected-variant-id').val(currentVariantId);

                    // Cập nhật số lượng tối đa có thể mua
                    $('.quantity-input').attr('max', matchedVariant.quantity);
                    if (parseInt($('.quantity-input').val()) > matchedVariant.quantity) {
                        $('.quantity-input').val(matchedVariant.quantity);
                    }
                    // Cập nhật giá
                    updatePrice(matchedVariant);

                    // Hiển thị thông tin còn lại
                    updateStockInfo(matchedVariant.quantity);

                    return true; // Đã tìm thấy biến thể phù hợp
                } else {
                    // Reset ID biến thể nếu không tìm thấy phù hợp
                    currentVariantId = '';
                    $('#selected-variant-id').val('');

                    // Reset về giá mặc định
                    $('.current-price').text('$' + parseFloat("{{ $product['current_price'] }}").toFixed(2));

                    if (parseFloat("{{ $product['discount'] }}") > 0) {
                        $('.original-price').text('$' + parseFloat("{{ $product['original_price'] }}").toFixed(2))
                            .show();
                        $('.discount-badge').text('-{{ $product['discount'] }}%').show();
                    } else {
                        $('.original-price, .discount-badge').hide();
                    }

                    updateStockInfo(null);
                    return false; // Không tìm thấy biến thể phù hợp
                }
            }

            // Hiển thị thông tin tồn kho
            function updateStockInfo(quantity) {
                // Xóa thông báo cũ nếu có
                $('.stock-info').remove();

                if (quantity === null) {
                    // Không có biến thể phù hợp
                    $('.product-stock-info').after(
                        '<div class="stock-info text-secondary">Please select product variation.</div>');
                } else if (quantity <= 0) {
                    // Hết hàng
                    $('.product-stock-info').after('<div class="stock-info text-danger">Out of stock</div>');
                    $('.add-to-cart-btn').prop('disabled', true).addClass('disabled');
                } else if (quantity <= 5) {
                    // Sắp hết hàng
                    $('.product-stock-info').after(
                        `<div class="stock-info text-warning">Only ${quantity} products left</div>`);
                    $('.add-to-cart-btn').prop('disabled', false).removeClass('disabled');
                } else {
                    // Còn nhiều hàng
                    $('.product-stock-info').after('<div class="stock-info text-success">In stock</div>');
                    $('.add-to-cart-btn').prop('disabled', false).removeClass('disabled');
                }
            }

            // Cập nhật hiển thị giá dựa trên biến thể
            function updatePrice(variant) {
                // Chuyển đổi sang số và cung cấp giá trị mặc định
                const discountedPrice = parseFloat(variant.discounted_price || variant.price || 0);
                const originalPrice = parseFloat(variant.price || 0);
                let discount = 0;

                if (originalPrice > discountedPrice) {
                    discount = Math.round(100 - (discountedPrice / originalPrice * 100));
                }

                $('.current-price').text('$' + discountedPrice.toFixed(2));

                if (discount > 0) {
                    $('.original-price').text('$' + originalPrice.toFixed(2)).show();
                    $('.discount-badge').text('-' + discount + '%').show();
                } else {
                    $('.original-price, .discount-badge').hide();
                }
            }

            function highlightMissingOptions(missingOptions) {
                missingOptions.forEach(option => {
                    let $container;
                    if (option === 'color') {
                        $container = $('.color-options');
                    } else if (option === 'size') {
                        $container = $('.size-options');
                    }

                    if ($container) {
                        // Thêm hiệu ứng nhấp nháy
                        $container.addClass('needs-attention');

                        // Cuộn đến phần tử đó
                        $('html, body').animate({
                            scrollTop: $container.offset().top - 100
                        }, 500);

                        // Xóa hiệu ứng sau 2 giây
                        setTimeout(() => {
                            $container.removeClass('needs-attention');
                        }, 2000);
                    }
                });
            }

            // ------ GẮN SỰ KIỆN -------

            // Sự kiện thumbnail
            $thumbnails.on('click', function() {
                const index = $(this).data('index');
                updateMainImage(index);
            });

            $moreImages.on('click', function() {
                $('#imageGalleryModal').modal('show');
            });

            // Điều hướng hình ảnh
            $prevBtn.on('click', function() {
                currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : productImages.length -
                    1;
                updateMainImage(currentImageIndex);
            });

            $nextBtn.on('click', function() {
                currentImageIndex = currentImageIndex < productImages.length - 1 ? currentImageIndex + 1 :
                    0;
                updateMainImage(currentImageIndex);
            });

            // Điều khiển Gallery trong modal
            const $galleryImage = $('.gallery-image');
            const $galleryThumbs = $('.gallery-thumb');
            const $galleryPrev = $('.gallery-prev');
            const $galleryNext = $('.gallery-next');

            $galleryThumbs.on('click', function() {
                updateGalleryImage($(this).data('index'));
            });

            $galleryPrev.on('click', function() {
                galleryCurrentIndex = (galleryCurrentIndex - 1 + productImages.length) % productImages
                    .length;
                updateGalleryImage(galleryCurrentIndex);
            });

            $galleryNext.on('click', function() {
                galleryCurrentIndex = (galleryCurrentIndex + 1) % productImages.length;
                updateGalleryImage(galleryCurrentIndex);
            });

            $('#imageGalleryModal').on('show.bs.modal', function() {
                galleryCurrentIndex = currentImageIndex;
                updateGalleryImage(galleryCurrentIndex);
            });

            // Sự kiện chọn màu
            $('.color-option').on('click', function() {
                if ($(this).hasClass('disabled')) return;

                // Nếu đang được chọn rồi thì bỏ chọn
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    currentColorName = null;

                    // Khi bỏ chọn màu, reset lại tất cả các tùy chọn
                    $('.size-option').removeClass('disabled');
                    updateProductImages(''); // Hiển thị hình ảnh mặc định
                    findAndUpdateVariant();
                    return;
                }

                $('.color-option').removeClass('active');
                $(this).addClass('active');

                // Cập nhật màu đã chọn
                if ($(this).hasClass('default-option')) {
                    currentColorName = '';
                } else {
                    currentColorName = $(this).data('color-name');
                }

                // Cập nhật các tùy chọn size và hình ảnh
                updateSizeOptions(currentColorName);
                updateProductImages(currentColorName);
            });

            // Sự kiện chọn size
            $('.size-option').on('click', function() {
                if ($(this).hasClass('disabled')) return;

                // Nếu đang được chọn rồi thì bỏ chọn
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    currentSize = null;

                    // Khi bỏ chọn size, reset lại tất cả các tùy chọn
                    $('.color-option').removeClass('disabled');
                    findAndUpdateVariant();
                    return;
                }

                $('.size-option').removeClass('active');
                $(this).addClass('active');

                // Cập nhật size đã chọn
                currentSize = $(this).data('size');

                // Cập nhật các tùy chọn màu
                updateColorOptions(currentSize);
            });

            // Điều khiển số lượng
            $('.quantity-btn.plus').on('click', function() {
                let $input = $('.quantity-input');
                let currentValue = parseInt($input.val());
                let maxQuantity = parseInt($input.attr('max') || 9999);

                if (currentValue < maxQuantity) {
                    $input.val(currentValue + 1);
                } else {
                    showToast(`Can only buy up to ${maxQuantity} products`, 'warning');
                }
            });

            $('.quantity-btn.minus').on('click', function() {
                let $input = $('.quantity-input');
                let currentValue = parseInt($input.val());
                if (currentValue > 1) {
                    $input.val(currentValue - 1);
                }
            });

            // Thêm vào giỏ hàng
            $('.add-to-cart-btn').on('click', function() {
                // Xác định xem trong lựa chọn hiện tại (size và màu) có cần cả hai không
                // hoặc chỉ cần một trong hai

                // Tạo biến để theo dõi các biến thể đặc biệt
                const hasSizeOnlyVariants = allVariants.some(v => v.size && (!v.color_name || v
                    .color_name === ''));
                const hasColorOnlyVariants = allVariants.some(v => v.color_name && (!v.size || v.size ===
                    ''));
                const hasDefaultVariant = allVariants.some(v => (!v.size || v.size === '') && (!v
                    .color_name || v.color_name === ''));

                console.log("Debug variants:", {
                    hasSizeOnlyVariants,
                    hasColorOnlyVariants,
                    hasDefaultVariant,
                    currentSize,
                    currentColorName
                });

                // Kiểm tra từng trường hợp cụ thể
                if (currentSize && !currentColorName) {
                    // Đã chọn size nhưng chưa chọn màu

                    // Kiểm tra xem biến thể chỉ có size này có tồn tại không
                    const sizeOnlyVariant = allVariants.find(v =>
                        v.size === currentSize && (!v.color_name || v.color_name === '')
                    );

                    if (sizeOnlyVariant) {
                        // Có biến thể chỉ cần size, không cần màu
                        currentVariantId = sizeOnlyVariant.id;
                        $('#selected-variant-id').val(currentVariantId);
                    } else {
                        // Cần chọn thêm màu cho size này
                        const availableColors = allVariants
                            .filter(v => v.size === currentSize)
                            .map(v => v.color_name)
                            .filter(Boolean);

                        if (availableColors.length > 0) {
                            showToast(`Please select a color for size ${currentSize}`, 'warning');
                            highlightMissingOptions(['color']);
                            return;
                        }
                    }
                } else if (!currentSize && currentColorName) {
                    // Đã chọn màu nhưng chưa chọn size

                    // Kiểm tra xem biến thể chỉ có màu này có tồn tại không
                    const colorOnlyVariant = allVariants.find(v =>
                        v.color_name === currentColorName && (!v.size || v.size === '')
                    );

                    if (colorOnlyVariant) {
                        // Có biến thể chỉ cần màu, không cần size
                        currentVariantId = colorOnlyVariant.id;
                        $('#selected-variant-id').val(currentVariantId);
                    } else {
                        // Cần chọn thêm size cho màu này
                        const availableSizes = allVariants
                            .filter(v => v.color_name === currentColorName)
                            .map(v => v.size)
                            .filter(Boolean);

                        if (availableSizes.length > 0) {
                            showToast(`Please select a size for color ${currentColorName}`, 'warning');
                            highlightMissingOptions(['size']);
                            return;
                        }
                    }
                } else if (!currentSize && !currentColorName) {
                    // Chưa chọn gì cả

                    // Kiểm tra xem có biến thể mặc định không
                    if (hasDefaultVariant) {
                        // Có biến thể mặc định, không cần chọn gì
                        const defaultVariant = allVariants.find(v =>
                            (!v.size || v.size === '') && (!v.color_name || v.color_name === '')
                        );

                        if (defaultVariant) {
                            currentVariantId = defaultVariant.id;
                            $('#selected-variant-id').val(currentVariantId);
                        }
                    } else {
                        // Không có biến thể mặc định, xác định cần chọn gì
                        let missingOptions = [];
                        let message = '';

                        // Nếu sản phẩm có các biến thể với size hoặc màu, yêu cầu chọn
                        if (allVariants.some(v => v.size)) missingOptions.push('size');
                        if (allVariants.some(v => v.color_name)) missingOptions.push('color');

                        if (missingOptions.length === 1) {
                            message = `Please select a ${missingOptions[0]} for this product`;
                        } else if (missingOptions.length > 1) {
                            message = `Please select both color and size for this product`;
                        }

                        showToast(message, 'warning');
                        highlightMissingOptions(missingOptions);
                        return;
                    }
                }

                // Kiểm tra lại xem biến thể có khớp không
                const validSelection = findAndUpdateVariant();

                if (!validSelection || currentVariantId === '') {
                    // Hiển thị thông báo lỗi chi tiết
                    if (currentSize && currentColorName) {
                        showToast(
                            `No variant available with size ${currentSize} and color ${currentColorName}`,
                            'warning');
                    } else if (currentSize) {
                        showToast(`No variant available with size ${currentSize}`, 'warning');
                    } else if (currentColorName) {
                        showToast(`No variant available with color ${currentColorName}`, 'warning');
                    } else {
                        showToast('No product variant available', 'warning');
                    }
                    return;
                }

                // Lấy biến thể đã chọn để kiểm tra số lượng
                const selectedVariant = allVariants.find(v => v.id === parseInt(currentVariantId));
                const quantity = parseInt($('.quantity-input').val(), 10);

                if (!selectedVariant) {
                    showToast('Could not find matching product variant', 'warning');
                    return;
                }

                if (selectedVariant.quantity <= 0) {
                    showToast('Product is out of stock', 'warning');
                    return;
                }

                if (quantity > selectedVariant.quantity) {
                    showToast(`Only ${selectedVariant.quantity} products left in stock`, 'warning');
                    $('.quantity-input').val(selectedVariant.quantity);
                    return;
                }

                // Hiển thị thông tin biến thể đã chọn
                let variantInfo = '';
                if (selectedVariant.color_name) variantInfo += `Color: ${selectedVariant.color_name}, `;
                if (selectedVariant.size) variantInfo += `Size: ${selectedVariant.size}, `;
                variantInfo += `Quantity: ${quantity}`;

                // Gửi yêu cầu AJAX để thêm sản phẩm vào giỏ hàng
                console.log('Adding to cart:', {
                    variantId: currentVariantId,
                    quantity: quantity,
                    colorName: selectedVariant.color_name || 'Default',
                    size: selectedVariant.size || 'Default'
                });

                // Hiển thị thông báo thành công
                showToast('Product added to cart successfully', 'success');

                // Ví dụ gọi AJAX (bỏ comment và điều chỉnh nếu cần)
                // $.post('/cart/add', {
                //     variant_id: currentVariantId,
                //     quantity: quantity
                // }).done(function(response) {
                //     showToast('Product added to cart successfully', 'success');
                // }).fail(function(error) {
                //     showToast('Error adding product to cart', 'error');
                // });
            });

            // Vuốt trên thiết bị di động
            let startX = 0,
                startY = 0,
                isDragging = false;
            $mainImageContainer.on('touchstart', function(e) {
                const touch = e.originalEvent.touches[0];
                startX = touch.clientX;
                startY = touch.clientY;
                isDragging = true;
            });

            $mainImageContainer.on('touchmove', function(e) {
                if (!isDragging) return;
                e.preventDefault();
            });

            $mainImageContainer.on('touchend', function(e) {
                if (!isDragging) return;
                isDragging = false;

                const touch = e.originalEvent.changedTouches[0];
                const diffX = startX - touch.clientX;
                const diffY = startY - touch.clientY;

                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                    currentImageIndex = diffX > 0 ?
                        (currentImageIndex + 1) % productImages.length :
                        (currentImageIndex - 1 + productImages.length) % productImages.length;
                    updateMainImage(currentImageIndex);
                }
            });

            // Khởi tạo ban đầu
            updateSizeOptions(currentColorName);
            updateColorOptions(currentSize);

            if (currentVariantId) {
                const initialVariant = allVariants.find(v => v.id === parseInt(currentVariantId));
                if (initialVariant) {
                    updateStockInfo(initialVariant.quantity);
                    $('.quantity-input').attr('max', initialVariant.quantity);
                }
            }
        });
    </script>
@endpush
