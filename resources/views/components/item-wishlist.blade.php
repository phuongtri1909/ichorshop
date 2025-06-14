@props(['wishlistItem', 'product' => $wishlistItem->product])

<div class="wishlist-card">
    <div class="">
        <div class="rounded-2 d-flex align-items-center">
            <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="text-decoration-none">
                <img src="{{ $product->avatar ? Storage::url($product->avatar) : asset('assets/images/default/product-default.png') }}"
                    alt="{{ $product->name }}" class="wishlist-image rounded-1">
            </a>

            <div class="ms-4 flex-grow-1 ">
                <a href="{{ route('product.details', ['slug' => $product->slug]) }}" class="text-decoration-none">
                    <h6 class="color-primary mb-1">{{ $product->name }}</h6>
                </a>
                <p class="text-muted mb-2">{{ $product->created_at->diffForHumans() }}</p>


                <form action="{{ route('user.wishlist.destroy', $wishlistItem->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger remove-wishlist-btn">
                        <i class="fas fa-trash me-1"></i> Remove
                    </button>
                </form>

            </div>

            <div class="ms-auto text-end d-flex items-center">
                <p class="fs-5 mb-0 me-3 d-flex align-items-center">
                    @php
                        $minPrice = $product->getMinPrice();
                        $discountedPrice = $product->getMinDiscountedPrice();
                        $hasDiscount = $product->hasDiscount();
                    @endphp

                    @if ($hasDiscount)
                        <span class="text-decoration-line-through text-muted">
                           ${{ number_format($minPrice, 2) }}
                        </span>
                        <span class="text-danger ms-2">${{ number_format($discountedPrice, 2) }}
                        </span>
                    @else
                        <span>${{ number_format($minPrice, 2) }} </span>
                    @endif
                </p>

                <button type="button" class="btn btn-sm btn-outline-dark add-to-cart"
                    data-product-id="{{ $product->id }}">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .wishlist-image {
                width: 100px;
                height: 100px;
                object-fit: scale-down;
                background: var(--primary-color-2);
                transition: transform 0.3s ease;
            }

            .wishlist-image:hover {
                transform: scale(1.05);
            }

            .wishlist-card {
                transition: all 0.3s ease;
                border-radius: 8px;
                padding: 10px;
            }

            .wishlist-card:hover {
                background-color: var(--primary-color-1);
            }

            .cl-ffe371 {
                color: #ffe371;
            }

            .remove-wishlist-btn {
                font-size: 0.8rem;
                padding: 0.25rem 0.75rem;
            }

            .add-to-cart {
                padding: 0.375rem 0.75rem;
                transition: all 0.3s ease;
            }

            .add-to-cart:hover {
                background-color: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add to cart functionality
                document.querySelectorAll('.add-to-cart').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const productId = this.getAttribute('data-product-id');

                        // Add AJAX request to add item to cart
                        fetch('/cart/add', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    product_id: productId,
                                    quantity: 1
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Show success notification
                                    // You can use toastr, sweetalert2, or any notification library
                                    alert('Product added to cart!');

                                    // Optional: Update cart count in header
                                    // updateCartCount(data.cartCount);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    });
                });
            });
        </script>
    @endpush
@endonce
