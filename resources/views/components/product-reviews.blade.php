<div class="product-reviews">
    <!-- Reviews Header -->
    <div class="reviews-header d-flex justify-content-between align-items-center mb-4">
        <h5><span class="fw-bold">All Reviews</span> ({{ $reviewsCount }})</h5>
        <div class="reviews-actions d-flex align-items-center">
            <select class="form-select review-sort rounded-5" id="review-sort">
                <option value="latest" {{ ($currentSort ?? '') == 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="oldest" {{ ($currentSort ?? '') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="highest" {{ ($currentSort ?? '') == 'highest' ? 'selected' : '' }}>Highest Rated</option>
                <option value="lowest" {{ ($currentSort ?? '') == 'lowest' ? 'selected' : '' }}>Lowest Rated</option>
            </select>
            @auth
                @if ($product['canBeReviewed'] ?? false)
                    <a href="{{ route('user.reviews.create', ['product' => $product['id']]) }}"
                        class="btn btn-pry write-review-btn ms-3 w-100 rounded-5">Write a Review</a>
                @endif
            @else
                <a href="{{ route('login') }}?redirect={{ url()->current() }}"
                    class="btn btn-pry write-review-btn ms-3 w-100 rounded-5">Login to Review</a>
            @endauth
        </div>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list row" id="reviews-container">
        @forelse ($reviews as $review)
            <div class="col-12 col-md-6 mb-4">
                <x-item_testimonial :review="$review" />
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3"><i class="far fa-comment-dots fa-3x text-muted"></i></div>
                <h5>No Reviews Yet</h5>
                <p class="text-muted">Be the first to review this product</p>
            </div>
        @endforelse
    </div>

    <!-- Load More Button -->
    @if ($reviews->hasMorePages())
        <div class="text-center mt-4">
            <button class="btn btn-outline-dark load-more-reviews border-primary-5" id="load-more-btn" data-page="2"
                data-product-id="{{ $product['id'] }}">
                Load More Reviews
            </button>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sort reviews
            const sortSelect = document.getElementById('review-sort');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const sortBy = this.value;
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('sort', sortBy);

                    // Redirect with the sort parameter
                    window.location.href = currentUrl.toString();
                });
            }

            // Load more reviews
            const loadMoreBtn = document.getElementById('load-more-btn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    const button = this;
                    const page = button.getAttribute('data-page');
                    const productId = button.getAttribute('data-product-id');
                    const currentSort = new URLSearchParams(window.location.search).get('sort') || 'latest';

                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    button.disabled = true;

                    fetch(`/products/${productId}/reviews/load-more?page=${page}&sort=${currentSort}`)
                        .then(response => response.text())
                        .then(html => {
                            const reviewsContainer = document.getElementById('reviews-container');
                            reviewsContainer.insertAdjacentHTML('beforeend', html);

                            // Update page number for next load
                            button.setAttribute('data-page', parseInt(page) + 1);
                            button.innerHTML = 'Load More Reviews';
                            button.disabled = false;

                            // Check if we've reached the last page
                            if (html.includes('No more reviews to load') || html.trim() === '') {
                                button.remove();
                            }
                        })
                        .catch(error => {
                            console.error('Error loading more reviews:', error);
                            button.innerHTML = 'Try Again';
                            button.disabled = false;
                        });
                });
            }
        });
    </script>
@endpush
