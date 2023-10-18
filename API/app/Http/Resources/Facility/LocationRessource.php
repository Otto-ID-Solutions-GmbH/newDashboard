<?php

namespace Cintas\Http\Resources\Facility;

use Cintas\Facades\Statistics;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationRessource extends JsonResource
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
            '__typename' => Statistics::getMorphAliasFromModel($this->resource),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'name' => $this->name,
            'label' => $this->label,
        ];
    }
}
