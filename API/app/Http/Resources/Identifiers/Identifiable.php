<?php

namespace Cintas\Http\Resources\Identifiers;

use Cintas\Facades\Statistics;
use Cintas\Http\Resources\Items\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class Identifiable extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'cuid' => $this->cuid,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'label' => $this->label,
            '__typename' => Statistics::getMorphAliasFromModel($this->resource),

            'product' => $this->when($this->product ?? null, Product::make($this->whenLoaded('product')))
        ];
    }
}
