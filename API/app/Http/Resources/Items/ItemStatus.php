<?php

namespace Cintas\Http\Resources\Items;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemStatus extends JsonResource
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
            'cuid' => $this->cuid,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'status' => $this->status_text,
            'label' => $this->label
        ];
    }
}
