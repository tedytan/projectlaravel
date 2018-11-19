<?php

namespace App\Http\Resources;

use App\Http\Resources\ImagesProductsResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'product' => ucfirst($this->product),
            'price' => (int) $this->price,
            'stock' => (int) $this->stock,
            'description' => $this->description,
            'image' => ($this->image = null) ? null : url('upload/'.$this->image),
            'images' => ImagesProductsResource::collection($this->whenLoaded('imageRelation')),
        ];
    }
}
