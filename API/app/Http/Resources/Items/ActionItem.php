<?php

namespace Cintas\Http\Resources\Items;

use Carbon\Carbon;
use Cintas\Http\Resources\Identifiers\RfidTag;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionItem extends JsonResource
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
            'label' => $this->label,

            'read_at' => $this->whenPivotLoaded('item_scan_action', function () {
                return (new Carbon($this->pivot->read_at))->toIso8601String();
            }),
            'antenna' => $this->whenPivotLoaded('item_scan_action', function () {
                return $this->pivot->antenna;
            }),

            'cycle_count' => $this->cycle_count ?? 0,
            'remaining_lifetime' => $this->remaining_lifetime,
            'tags' => RfidTag::collection($this->rfid_tags)
        ];
    }
}
