@extends('client.layouts.information')

@section('info_title', 'Write Reviews - ' . request()->getHost())
@section('info_description', 'Share your experience with the products you ordered')
@section('info_keyword', 'Reviews, Product Reviews, ' . request()->getHost())

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'Write Review',
        'items' => $breadcrumbItems,
    ])
@endpush

@section('info_content')
    <div class="review-form-container bg-white rounded p-4">
        <h4 class="mb-4">Write Your Review</h4>
        <p>Order #{{ $order->order_code }} - Placed on {{ $order->created_at->format('F d, Y') }}</p>

        <form action="{{ route('user.reviews.store') }}" method="POST" id="reviewForm">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            
            <div class="form-group mb-4">
                <label for="product_id" class="form-label">Select Product</label>
                <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">-- Select a product to review --</option>
                    @foreach($reviewableProducts as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label d-block">Your Rating</label>
                <div class="star-rating">
                    <div class="rating-group">
                        <input type="radio" class="rating-input" id="rating-5" name="rating" value="5" {{ old('rating') == 5 ? 'checked' : '' }} required>
                        <label for="rating-5" class="rating-star">
                            <i class="fas fa-star"></i>
                        </label>
                        <input type="radio" class="rating-input" id="rating-4" name="rating" value="4" {{ old('rating') == 4 ? 'checked' : '' }}>
                        <label for="rating-4" class="rating-star">
                            <i class="fas fa-star"></i>
                        </label>
                        <input type="radio" class="rating-input" id="rating-3" name="rating" value="3" {{ old('rating') == 3 ? 'checked' : '' }}>
                        <label for="rating-3" class="rating-star">
                            <i class="fas fa-star"></i>
                        </label>
                        <input type="radio" class="rating-input" id="rating-2" name="rating" value="2" {{ old('rating') == 2 ? 'checked' : '' }}>
                        <label for="rating-2" class="rating-star">
                            <i class="fas fa-star"></i>
                        </label>
                        <input type="radio" class="rating-input" id="rating-1" name="rating" value="1" {{ old('rating') == 1 ? 'checked' : '' }}>
                        <label for="rating-1" class="rating-star">
                            <i class="fas fa-star"></i>
                        </label>
                    </div>
                    @error('rating')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-group mb-4">
                <label for="content" class="form-label">Your Review</label>
                <textarea name="content" id="content" rows="5" class="form-control @error('content') is-invalid @enderror" placeholder="Share your experience with this product..." required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text mt-1">Minimum 10 characters required</div>
            </div>
            
            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    Submit Review
                </button>
                <a href="{{ route('user.orders.detail', $order) }}" class="btn btn-outline-secondary ms-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .star-rating .rating-group {
        display: inline-flex;
        flex-direction: row-reverse;
    }
    
    .rating-input {
        position: absolute;
        left: -9999px;
    }
    
    .rating-star {
        font-size: 1.5rem;
        color: #ddd;
        cursor: pointer;
        margin-right: 5px;
        transition: color 0.3s;
    }
    
    .rating-input:checked ~ .rating-star,
    .rating-star:hover,
    .rating-input:focus ~ .rating-star,
    .rating-star:hover ~ .rating-star {
        color: #ffb800;
    }
</style>
@endpush