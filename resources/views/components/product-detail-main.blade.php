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
                                <div class="thumbnail-item {{ $i === 0 ? 'active' : '' }} flex-1" data-index="{{ $i }}">
                                    <img src="{{ asset($product['images'][$i]) }}" alt="Product {{ $i + 1 }}" class="img-fluid">
                                </div>
                            @elseif ($i < 2 && $totalImages > 3)
                                <!-- For 4+ images: Show first 2 images -->
                                <div class="thumbnail-item {{ $i === 0 ? 'active' : '' }} flex-1" data-index="{{ $i }}">
                                    <img src="{{ asset($product['images'][$i]) }}" alt="Product {{ $i + 1 }}" class="img-fluid">
                                </div>
                            @elseif ($i === 2 && $totalImages > 3)
                                <!-- For 4+ images: Show more indicator on 3rd slot -->
                                <div class="thumbnail-item more-images flex-1" data-toggle="modal" data-target="#imageGalleryModal">
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
                            @if($totalImages > 1)
                                <button class="image-nav prev-btn" type="button">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="image-nav next-btn" type="button">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                
                                <!-- Image indicators -->
                                <div class="image-indicators">
                                    @foreach($product['images'] as $index => $image)
                                        <span class="indicator {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></span>
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
                        </div>

                        <!-- Price -->
                        <div class="product-price mb-4">
                            <span class="current-price fw-bold">${{ $product['current_price'] }}</span>
                            @if (isset($product['original_price']))
                                <span class="original-price fw-bold">${{ $product['original_price'] }}</span>
                                <span class="discount-badge">-{{ $product['discount'] }}%</span>
                            @endif
                        </div>

                        <!-- Description -->
                       <p class="product-description color-primary-5 mb-4">{!! nl2br(e($product['description'])) !!}</p>

                    </div>

                    <div>
                        <!-- Colors -->
                        <div class="product-options mb-4">
                            <h6 class="option-title">Select Colors</h6>
                            <div class="color-options">
                                @foreach ($product['colors'] as $color)
                                    <div class="color-option {{ $loop->first ? 'active' : '' }}"
                                        data-color="{{ $color }}" style="background-color: {{ $color }};">
                                        <div class="color-circle color-{{ $color }}"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sizes -->
                        <div class="product-options mb-4">
                            <h6 class="option-title">Choose Size</h6>
                            <div class="size-options">
                                @foreach ($product['sizes'] as $size)
                                    <div class="size-option {{ $size === 'Large' ? 'active' : '' }}"
                                        data-size="{{ $size }}">
                                        {{ $size }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

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
<div class="modal fade" id="imageGalleryModal" tabindex="-1" role="dialog" aria-labelledby="imageGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageGalleryModalLabel">Product Gallery</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="gallery-container">
                    <div class="gallery-main-image">
                        <img src="{{ asset($product['images'][0]) }}" alt="Product Image" class="img-fluid gallery-image">
                        <button class="gallery-nav gallery-prev" type="button">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="gallery-nav gallery-next" type="button">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="gallery-thumbnails">
                        @foreach($product['images'] as $index => $image)
                            <div class="gallery-thumb {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
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
    $(document).ready(function () {
        const productImages = @json($product['images']);
        let currentImageIndex = 0;
        let galleryCurrentIndex = 0;

        const $mainImage = $('.main-image');
        const $thumbnails = $('.thumbnail-item:not(.more-images)');
        const $prevBtn = $('.prev-btn');
        const $nextBtn = $('.next-btn');
        const $indicators = $('.indicator');
        const $mainImageContainer = $('.main-image-container');
        const $moreImages = $('.thumbnail-item.more-images');

        function updateMainImage(index) {
            currentImageIndex = index;
            $mainImage.attr('src', '{{ asset('') }}' + productImages[index])
                      .attr('data-current-index', index);

            $indicators.removeClass('active').eq(index).addClass('active');
            $thumbnails.removeClass('active');
            
            // Only update thumbnail active state if it exists (may not for index > 1 when we have more than 3 images)
            $thumbnails.each(function() {
                if ($(this).data('index') == index) {
                    $(this).addClass('active');
                }
            });
        }

        $thumbnails.on('click', function () {
            const index = $(this).data('index');
            updateMainImage(index);
        });

        $moreImages.on('click', function() {
            $('#imageGalleryModal').modal('show');
        });

        $prevBtn.on('click', function () {
            currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : productImages.length - 1;
            updateMainImage(currentImageIndex);
        });

        $nextBtn.on('click', function () {
            currentImageIndex = currentImageIndex < productImages.length - 1 ? currentImageIndex + 1 : 0;
            updateMainImage(currentImageIndex);
        });

        // Swipe mobile
        let startX = 0, startY = 0, isDragging = false;
        $mainImageContainer.on('touchstart', function (e) {
            const touch = e.originalEvent.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            isDragging = true;
        });

        $mainImageContainer.on('touchmove', function (e) {
            if (!isDragging) return;
            e.preventDefault();
        });

        $mainImageContainer.on('touchend', function (e) {
            if (!isDragging) return;
            isDragging = false;

            const touch = e.originalEvent.changedTouches[0];
            const diffX = startX - touch.clientX;
            const diffY = startY - touch.clientY;

            if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                currentImageIndex = diffX > 0
                    ? (currentImageIndex + 1) % productImages.length
                    : (currentImageIndex - 1 + productImages.length) % productImages.length;
                updateMainImage(currentImageIndex);
            }
        });

        // Mouse drag
        let mouseStartX = 0, isMouseDragging = false;
        $mainImageContainer.on('mousedown', function (e) {
            mouseStartX = e.clientX;
            isMouseDragging = true;
            e.preventDefault();
        });

        $(document).on('mouseup', function (e) {
            if (!isMouseDragging) return;
            isMouseDragging = false;

            const diffX = mouseStartX - e.clientX;
            if (Math.abs(diffX) > 50) {
                currentImageIndex = diffX > 0
                    ? (currentImageIndex + 1) % productImages.length
                    : (currentImageIndex - 1 + productImages.length) % productImages.length;
                updateMainImage(currentImageIndex);
            }
        });

        // Gallery modal
        const $galleryImage = $('.gallery-image');
        const $galleryThumbs = $('.gallery-thumb');
        const $galleryPrev = $('.gallery-prev');
        const $galleryNext = $('.gallery-next');

        function updateGalleryImage(index) {
            galleryCurrentIndex = index;
            $galleryImage.attr('src', '{{ asset('') }}' + productImages[index]);
            $galleryThumbs.removeClass('active').eq(index).addClass('active');
        }

        $galleryThumbs.on('click', function () {
            updateGalleryImage($(this).index());
        });

        $galleryPrev.on('click', function () {
            galleryCurrentIndex = (galleryCurrentIndex - 1 + productImages.length) % productImages.length;
            updateGalleryImage(galleryCurrentIndex);
        });

        $galleryNext.on('click', function () {
            galleryCurrentIndex = (galleryCurrentIndex + 1) % productImages.length;
            updateGalleryImage(galleryCurrentIndex);
        });

        $('#imageGalleryModal').on('show.bs.modal', function () {
            updateGalleryImage(currentImageIndex);
        });

        // Color selection
        $('.color-option').on('click', function () {
            $('.color-option').removeClass('active');
            $(this).addClass('active');
        });

        // Size selection
        $('.size-option').on('click', function () {
            $('.size-option').removeClass('active');
            $(this).addClass('active');
        });

        // Quantity controls
        const $quantityInput = $('.quantity-input');
        $('.quantity-btn.minus').on('click', function () {
            const current = parseInt($quantityInput.val(), 10);
            if (current > 1) $quantityInput.val(current - 1);
        });

        $('.quantity-btn.plus').on('click', function () {
            const current = parseInt($quantityInput.val(), 10);
            $quantityInput.val(current + 1);
        });
    });
</script>
@endpush


