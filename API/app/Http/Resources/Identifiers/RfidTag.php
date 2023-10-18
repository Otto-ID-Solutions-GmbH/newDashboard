<?php

namespace Cintas\Http\Resources\Identifiers;

use Illuminate\Http\Resources\Json\JsonResource;

class RfidTag extends JsonResource
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
            'epc' => $this->epc,
            'epc_type' => $this->epc_type,
            'identifiable_id' => $this->identifiable_id,
            'identifiable_type' => $this->identifiable_type,
        ];
    }
}
