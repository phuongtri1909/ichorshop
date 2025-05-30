<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail' => asset('storage/' . $this->thumbnail),
            'avatar' =>  asset('storage/' . $this->avatar),
            'content' => $this->when($request->route()->getName() == 'news.show', $this->content),
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'related_news' => $this->when(isset($this->relatedNews), function () {
                return NewsResource::collection($this->relatedNews);
            })
        ];
    }
}