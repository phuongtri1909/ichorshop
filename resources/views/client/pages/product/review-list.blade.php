@foreach ($reviews as $review)
    <div class="col-12 col-md-6 mb-4">
        <x-item_testimonial :review="$review" />
    </div>
@endforeach