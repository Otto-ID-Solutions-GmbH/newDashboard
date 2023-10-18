<?php

namespace Cintas\Http\Resources\Actions;

use Cintas\Http\Resources\Facility\Reader;
use Cintas\Http\Resources\Items\ActionItem;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanAction extends JsonResource
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
            'type' => $this->type->name,
            'type_label' => $this->type->label,
            'out_scan' => OutScanAction::make($this->whenLoaded('out_scan_action')),
            'reader' => Reader::make($this->whenLoaded('reader')),
            'items_count' => $this->items_count,
            'skipped_items_count' => $this->skipped_items_count,
            'unknown_tags_count' => $this->unknown_tags_count,
            'items' => ActionItem::collection($this->whenLoaded('items'))
        ];
    }
}
