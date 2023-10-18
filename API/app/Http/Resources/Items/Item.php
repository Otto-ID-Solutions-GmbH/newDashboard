<?php

namespace Cintas\Http\Resources\Items;

use Cintas\Http\Resources\Items\Product as ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'cuid' => $this->cuid,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'label' => $this->label,

            'cycle_count' => $this->cycle_count ?? 0,
            'remaining_lifetime' => $this->remaining_lifetime,

            'product_id' => $this->product_id,
            'product' => ProductResource::make($this->whenLoaded('product')),

            'status_history' => ItemStatus::collection($this->whenLoaded('status_history')),
            'last_status_id' => $this->last_status_id,
            'last_status' => ItemStatus::make($this->whenLoaded('last_status')),

            'location' => Location::make($this->whenLoaded('location')),

        ];
    }
}
