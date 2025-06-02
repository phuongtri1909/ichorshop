<section class="product-detail-section pb-5">
    <div class="container">
        <div class="row h-100">
            <!-- Product Images -->
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="product-images w-100 d-flex gap-3">
                    <!-- Thumbnails -->
                    <div class="product-thumbnails d-flex gap-3">
                        @php
                            $totalImages = count($product['images']);
                            $showCount = min(3, $totalImages);
                        @endphp
                        
                        @for ($i = 0; $i < $showCount; $i++)
                            @if ($i < 2 || $totalImages <= 3)
                                <div class="thumbnail-item {{ $i === 0 ? 'active' : '' }}"
                                    data-index="{{ $i }}">
                                    <img src="{{ asset($product['images'][$i]) }}" alt="Product {{ $i + 1 }}" class="img-fluid">
                                </div>
                            @else
                                <!-- More images indicator -->
                                <div class="thumbnail-item more-images" data-toggle="modal" data-target="#imageGalleryModal">
                                    <img src="{{ asset($product['images'][$i]) }}" alt="Product {{ $i + 1 }}" class="img-fluid">
                                    <div class="more-overlay">
                                        <span class="more-count">+{{ $totalImages - 2 }}</span>
                                    </div>
                                </div>
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
                        <p class="product-description color-primary-5 mb-4">{{ $product['description'] }}</p>
                    </div>

                    <div>
                        <!-- Colors -->
                        <div class="product-options mb-4">
                            <h6 class="option-title">Select Colors</h6>
                            <div class="color-options">
                                @foreach ($product['colors'] as $color)
                                    <div class="color-option {{ $loop->first ? 'active' : '' }}"
                                        data-color="{{ $color }}">
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
        document.addEventListener('DOMContentLoaded', function() {
            const productImages = @json($product['images']);
            let currentImageIndex = 0;
            
            // Elements
            const thumbnails = document.querySelectorAll('.thumbnail-item:not(.more-images)');
            const mainImage = document.querySelector('.main-image');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            const indicators = document.querySelectorAll('.indicator');
            const mainImageContainer = document.querySelector('.main-image-container');

            // Update main image and thumbnails
            function updateMainImage(index) {
                currentImageIndex = index;
                mainImage.src = '{{ asset("") }}' + productImages[index];
                mainImage.setAttribute('data-current-index', index);
                
                // Update indicators
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle('active', i === index);
                });
                
                // Update thumbnail active state (only for visible thumbnails)
                thumbnails.forEach((thumbnail, i) => {
                    thumbnail.classList.toggle('active', i === index);
                });
            }

            // Thumbnail click handler
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', function() {
                    updateMainImage(index);
                });
            });

            // Navigation arrows
            if (prevBtn && nextBtn) {
                prevBtn.addEventListener('click', function() {
                    currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : productImages.length - 1;
                    updateMainImage(currentImageIndex);
                });

                nextBtn.addEventListener('click', function() {
                    currentImageIndex = currentImageIndex < productImages.length - 1 ? currentImageIndex + 1 : 0;
                    updateMainImage(currentImageIndex);
                });
            }

            // Indicator clicks
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', function() {
                    updateMainImage(index);
                });
            });

            // Touch/Swipe functionality for mobile
            let startX = 0;
            let startY = 0;
            let isDragging = false;

            mainImageContainer.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                isDragging = true;
            });

            mainImageContainer.addEventListener('touchmove', function(e) {
                if (!isDragging) return;
                e.preventDefault();
            });

            mainImageContainer.addEventListener('touchend', function(e) {
                if (!isDragging) return;
                isDragging = false;
                
                const endX = e.changedTouches[0].clientX;
                const endY = e.changedTouches[0].clientY;
                const diffX = startX - endX;
                const diffY = startY - endY;
                
                // Check if horizontal swipe is more significant than vertical
                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        // Swipe left - next image
                        currentImageIndex = currentImageIndex < productImages.length - 1 ? currentImageIndex + 1 : 0;
                    } else {
                        // Swipe right - previous image
                        currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : productImages.length - 1;
                    }
                    updateMainImage(currentImageIndex);
                }
            });

            // Mouse drag functionality for desktop
            let mouseStartX = 0;
            let isMouseDragging = false;

            mainImageContainer.addEventListener('mousedown', function(e) {
                mouseStartX = e.clientX;
                isMouseDragging = true;
                e.preventDefault();
            });

            document.addEventListener('mousemove', function(e) {
                if (!isMouseDragging) return;
                e.preventDefault();
            });

            document.addEventListener('mouseup', function(e) {
                if (!isMouseDragging) return;
                isMouseDragging = false;
                
                const diffX = mouseStartX - e.clientX;
                
                if (Math.abs(diffX) > 50) {
                    if (diffX > 0) {
                        // Drag left - next image
                        currentImageIndex = currentImageIndex < productImages.length - 1 ? currentImageIndex + 1 : 0;
                    } else {
                        // Drag right - previous image
                        currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : productImages.length - 1;
                    }
                    updateMainImage(currentImageIndex);
                }
            });

            // Gallery Modal functionality
            const galleryImage = document.querySelector('.gallery-image');
            const galleryThumbs = document.querySelectorAll('.gallery-thumb');
            const galleryPrev = document.querySelector('.gallery-prev');
            const galleryNext = document.querySelector('.gallery-next');
            let galleryCurrentIndex = 0;

            function updateGalleryImage(index) {
                galleryCurrentIndex = index;
                galleryImage.src = '{{ asset("") }}' + productImages[index];
                
                galleryThumbs.forEach((thumb, i) => {
                    thumb.classList.toggle('active', i === index);
                });
            }

            // Gallery thumbnail clicks
            galleryThumbs.forEach((thumb, index) => {
                thumb.addEventListener('click', function() {
                    updateGalleryImage(index);
                });
            });

            // Gallery navigation
            if (galleryPrev && galleryNext) {
                galleryPrev.addEventListener('click', function() {
                    galleryCurrentIndex = galleryCurrentIndex > 0 ? galleryCurrentIndex - 1 : productImages.length - 1;
                    updateGalleryImage(galleryCurrentIndex);
                });

                galleryNext.addEventListener('click', function() {
                    galleryCurrentIndex = galleryCurrentIndex < productImages.length - 1 ? galleryCurrentIndex + 1 : 0;
                    updateGalleryImage(galleryCurrentIndex);
                });
            }

            // Open gallery modal at current image
            $('#imageGalleryModal').on('show.bs.modal', function() {
                updateGalleryImage(currentImageIndex);
            });

            // Color selection
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(option => {
                option.addEventListener('click', function() {
                    colorOptions.forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Size selection
            const sizeOptions = document.querySelectorAll('.size-option');
            sizeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    sizeOptions.forEach(o => o.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Quantity controls
            const quantityInput = document.querySelector('.quantity-input');
            const minusBtn = document.querySelector('.quantity-btn.minus');
            const plusBtn = document.querySelector('.quantity-btn.plus');

            minusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            plusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                quantityInput.value = currentValue + 1;
            });
        });
    </script>
@endpush

