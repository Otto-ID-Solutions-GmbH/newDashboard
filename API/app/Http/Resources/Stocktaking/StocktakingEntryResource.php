<?php

namespace Cintas\Http\Resources\Stocktaking;

use Illuminate\Http\Resources\Json\JsonResource;

class StocktakingEntryResource extends JsonResource
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
            "stock_id" => $this->stock_id,
            "stock_label" => $this->stock_label,
            "is_amount" => $this->is_amount,
            "item_ids" => $this->item_ids,
        ];
    }
}
