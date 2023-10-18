<?php

namespace Cintas\Http\Resources\Actions;

use Cintas\Http\Resources\Items\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class OutScanAction extends JsonResource
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
            'created_at' => $this->created_at->toIso8601String(),
            'label' => $this->label,
            'target_location' => Location::make($this->whenLoaded('location'))
        ];
    }
}
