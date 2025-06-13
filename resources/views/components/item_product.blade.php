<a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="product-card text-decoration-none">
    <div class="product-image">
        <img src="{{ $product->avatar ? Storage::url($product->avatar) : asset('assets/images/default/product-default.png') }}"
            alt="{{ $product->name }}" class="img-fluid">
    </div>
    <div class="">
        <h5 class="product-name">{{ $product->name }}</h5>
        <div class="d-flex">
            <span class="rating-stars text-sm color-primary-5" title="{{ $product->rating ?? 0 }} sao">
                @php
                    $rating = $product->rating ?? 0;
                    $displayRating = round($rating * 2) / 2;
                @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($displayRating >= $i)
                        <i class="fas fa-star cl-ffe371 "></i>
                    @elseif ($displayRating >= $i - 0.5)
                        <i class="fas fa-star-half-alt cl-ffe371 "></i>
                    @else
                        <i class="far fa-star cl-ffe371 "></i>
                    @endif
                @endfor
                {{ $rating }}/5
            </span>
        </div>
        <div class="product-price">
            @php
                $minPrice = $product->getMinPrice();
                $discountedPrice = $product->getMinDiscountedPrice();
                $hasDiscount = $product->hasDiscount();
                $discountPercentage = $product->getDiscountPercentage();

            @endphp

            @if ($hasDiscount)
                <span class="current-price">${{ number_format($discountedPrice, 2) }}</span>
                <span class="original-price color-primary-5">${{ number_format($minPrice, 2) }}</span>
                <span class="discount">-{{ $discountPercentage }}%</span>
            @else
                <span class="current-price">${{ number_format($minPrice, 2) }}</span>
            @endif
        </div>
    </div>
</a>
@once
    @push('styles')
        <style>
            .product-name {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--primary-color);
                margin-bottom: 0.5rem;
            }

            .product-image {
                background-color: var(--primary-color-2);
                aspect-ratio: 1;
                overflow: hidden;
                border-radius: 20px;
                margin-bottom: 1rem;
            }

            .product-image img {
                width: 100%;
                height: 100%;
                object-fit: scale-down;
                transition: transform 0.3s ease;
            }

            .product-card:hover .product-image img {
                transform: scale(1.05);
            }

            .discount {
                background: rgba(255, 51, 51, 0.1);
                color: #ff3333;
                padding: 6px 14px;
                border-radius: 62px;
                font-size: 12px;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .product-name {
                    font-size: 1rem;
                }
            }
        </style>
    @endpush
@endonce
