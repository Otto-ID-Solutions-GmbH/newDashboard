<?php

namespace Cintas\Http\Resources\Actions;

use Illuminate\Http\Resources\Json\JsonResource;

class ScanActionType extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
