<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\WeightResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'category_name' => $this->category->name ?? null,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'highlight' => $this->highlight,
            'image' => asset('storage/' . $this->image),
            'is_featured' => $this->is_featured,
            'min_price' => $this->min_price,
            'min_discounted_price' => $this->min_discounted_price,
            'average_rating' => $this->average_rating,
            'review_count' => $this->review_count,
            'weights' => WeightResource::collection($this->whenLoaded('weights')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}