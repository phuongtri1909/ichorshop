 <div class="product-card">
     <div class="product-image">
         <img src="https://picsum.photos/300/400?random=2" alt="T-shirt" class="img-fluid">
     </div>
     <div class="product-info">
         <h5 class="product-name">T-shirt with Tape Details</h5>
         <div class="d-flex">
            <span class="rating-stars text-sm" title="5 sao">
                @php
                    $rating = 4.5 ?? 0;
                   
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
             <span class="current-price">$130</span>
             <span class="original-price color-primary-5">$160</span>
             <span class="discount">-30%</span>
         </div>
     </div>
 </div>
 @push('styles')
     <style>
         .product-name {
             font-size: 1.25rem;
             font-weight: 700;
             color: var(--primary-color);
             margin-bottom: 0.5rem;
         }

         @media (max-width: 768px) {
             .product-name {
                 font-size: 1rem;
             }
         }
     </style>
 @endpush
