<div class="product-reviews">
    <!-- Reviews Header -->
    <div class="reviews-header d-flex justify-content-between align-items-center mb-4">
        <h5>All Reviews (451)</h5>
        <div class="reviews-actions">
            <select class="form-select review-sort">
                <option value="latest">Latest</option>
                <option value="oldest">Oldest</option>
                <option value="highest">Highest Rated</option>
                <option value="lowest">Lowest Rated</option>
            </select>
            <button class="btn btn-outline-dark write-review-btn ms-3">Write a Review</button>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list row">
        @foreach ($testimonials as $testimonial)
            <div class="col-12 col-md-6 mb-4">
                <x-item_testimonial :testimonial="$testimonial" />
            </div>
        @endforeach
    </div>

    <!-- Load More Button -->
    <div class="text-center mt-4">
        <button class="btn btn-outline-dark load-more-reviews border-primary-5">Load More Reviews</button>
    </div>
</div>
