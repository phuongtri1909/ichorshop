@foreach($products as $product)
    @if($product->variants && $product->variants->count() > 0)
        <div class="product-item mb-3">
            <div class="product-header">
                <div class="form-check">
                    <input class="form-check-input product-checkbox" 
                           type="checkbox" 
                           id="product_{{ $product->id }}" 
                           data-product-id="{{ $product->id }}">
                    <label class="form-check-label" for="product_{{ $product->id }}">
                        <span class="product-name">{{ $product->name }}</span>
                        @if(isset($product->sku))
                            <span class="product-sku text-muted">(SKU: {{ $product->sku }})</span>
                        @endif
                    </label>
                </div>
            </div>
            <div class="row mt-2" id="variants_{{ $product->id }}">
                @foreach($product->variants as $variant)
                    <div class="variant-item col-3">
                        <div class="form-check">
                            <input class="form-check-input variant-checkbox" 
                                   type="checkbox" 
                                   name="product_variants[]" 
                                   value="{{ $variant->id }}" 
                                   id="variant_{{ $variant->id }}" 
                                   data-product-id="{{ $product->id }}"
                                   {{ isset($selectedVariantIds) && in_array($variant->id, $selectedVariantIds ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label" for="variant_{{ $variant->id }}">
                                @if($variant->color_name)
                                    <span class="variant-color">{{ $variant->color_name }}</span>
                                @endif
                                @if($variant->size)
                                    <span class="variant-size">Size: {{ $variant->size }}</span>
                                @endif
                                <span class="variant-price">${{ $variant->price }}</span>
                                @if($variant->sku)
                                    <span class="variant-sku text-muted">({{ $variant->sku }})</span>
                                @endif
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach