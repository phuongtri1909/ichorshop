<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FranchiseResource extends JsonResource
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
            'name' => $this->name,
            'name_package' => $this->name_package,
            'slug' => $this->slug,
            'code' => $this->code,
            'sort_order' => $this->sort_order,
            'description' => $this->description,
            'details' => json_decode($this->details),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}