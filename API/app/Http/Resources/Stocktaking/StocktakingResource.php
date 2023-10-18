<?php

namespace Cintas\Http\Resources\Stocktaking;

use Illuminate\Http\Resources\Json\JsonResource;

class StocktakingResource extends JsonResource
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
            "cuid" => $this->cuid,
            'created_at' => $this->created_at,// ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at,// ? $this->updated_at->toIso8601String() : null,
            "responsible_person_name" => $this->responsible_person_name,
            "notes" => $this->notes,
            "location_id" => $this->location_id,
            "location_type" => $this->location_type,
            "stocktaking_entries" => StocktakingEntryResource::collection(collect($this->stocktaking_entries))
        ];
    }
}
