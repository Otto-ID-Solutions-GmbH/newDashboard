<?php

namespace Cintas\Http\Resources\Items;

use Cintas\Http\Resources\Facility\LaundryCustomer as LaundryCustomerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductTypeResource extends JsonResource
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
            "cuid" => $this->cuid,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            "name" => $this->name,
            "label" => $this->label,
            "expected_lifetime" => $this->expected_lifetime,
            "bundle_target" => $this->bundle_target
        ];
    }
}
