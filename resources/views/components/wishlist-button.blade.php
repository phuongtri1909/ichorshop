
@props([
    'product',
    'class' => '',
])

@php
    // Xác định ID sản phẩm dù sản phẩm là object hay array
    $productId = is_array($product) ? $product['id'] : $product->id;
    
    $isWishlisted = false;
    if (auth()->check()) {
        $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->exists();
    }
@endphp

<button type="button" 
    class="wishlist-btn {{ $class }}" 
    data-product-id="{{ $productId }}"
    data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}">
    <i class="{{ $isWishlisted ? 'fas' : 'far' }} fa-heart wishlist-icon"></i>
</button>

@once
    @push('styles')
        <style>
            .wishlist-btn {
                background: none;
                border: none;
                padding: 0;
                cursor: pointer;
                transition: transform 0.2s ease;
                color: var(--primary-color);
            }
            
            .wishlist-btn:hover {
                transform: scale(1.2);
            }
            
            .wishlist-btn:focus {
                outline: none;
            }
            
            .wishlist-icon.fas {
                color: #ff3366;
            }
            
            .wishlist-icon.far {
                color: inherit;
            }
            
            @keyframes heartBeat {
                0% { transform: scale(1); }
                25% { transform: scale(1.3); }
                50% { transform: scale(1); }
                75% { transform: scale(1.3); }
                100% { transform: scale(1); }
            }
            
            .heart-beat {
                animation: heartBeat 0.8s ease-in-out;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.wishlist-btn').forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const productId = this.getAttribute('data-product-id');
                        const isWishlisted = this.getAttribute('data-wishlisted') === 'true';
                        const icon = this.querySelector('.wishlist-icon');
                        
                        // Check if user is authenticated
                        @if(auth()->check())
                            // Make AJAX request to toggle wishlist
                            fetch('/user/wishlist/toggle', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    product_id: productId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Toggle wishlist icon
                                    if (data.wishlisted) {
                                        icon.classList.remove('far');
                                        icon.classList.add('fas');
                                        this.setAttribute('data-wishlisted', 'true');
                                    } else {
                                        icon.classList.remove('fas');
                                        icon.classList.add('far');
                                        this.setAttribute('data-wishlisted', 'false');
                                    }
                                    
                                    // Add heartbeat animation
                                    icon.classList.add('heart-beat');
                                    setTimeout(() => {
                                        icon.classList.remove('heart-beat');
                                    }, 800);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                        @else
                            // Redirect to login page or show login modal
                            window.location.href = '{{ route('login') }}?redirect=' + window.location.pathname;
                        @endif
                    });
                });
            });
        </script>
    @endpush
@endonce