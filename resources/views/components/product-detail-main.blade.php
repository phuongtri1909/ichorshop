<section class="product-detail-section py-5">
    <div class="container">
        <div class="row h-100">
            <!-- Product Images -->
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="product-images w-100 d-flex gap-3">
                    <!-- Thumbnails -->
                    <div class="product-thumbnails d-flex flex-column gap-3">
                        @foreach ($product['images'] as $index => $image)
                            <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                                <img src="{{ asset($image) }}" alt="Product {{ $index + 1 }}" class="img-fluid">
                            </div>
                        @endforeach
                    </div>

                    <!-- Main Image -->
                    <div class="product-main-image rounded-4 flex-1">
                        <img src="{{ asset($product['images'][0]) }}" alt="{{ $product['name'] }}"
                            class="img-fluid main-image w-100 h-100 rounded-4">
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6 d-flex align-items-stretch">
                <div class="product-info w-100 d-flex flex-column justify-content-between">
                    <h1 class="product-title">{{ $product['name'] }}</h1>

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
                    <div class="product-actions">
                        <div class="quantity-selector">
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
</section>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Thumbnail click handler
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            const mainImage = document.querySelector('.main-image');

            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const newSrc = this.querySelector('img').src;
                    mainImage.src = newSrc;
                });
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
