<?php

namespace Cintas\Models\Actions;

use Cintas\Models\AbstractModel;
use Cintas\Models\Items\Product;

class OutScanAction extends AbstractModel
{

    protected $with = ['scan_action'];

    protected $appends = ['avg_container_reach'];

    public function scan_action()
    {
        return $this->belongsTo(ScanAction::class, 'scan_action_id');
    }

    public function location()
    {
        return $this->morphTo('location');
    }

    public function getAvgContainerReachAttribute()
    {

        $scanAction = $this->scan_action;

        $totalItemCount = $scanAction->items->count();
        $totalContainerTarget = $scanAction->items->reduce(function ($result, $item) {
            return $result + $this->getTargetContainerContent($item->product);
        }, 0);

        return $totalItemCount / $totalContainerTarget;
    }

    private function getTargetContainerContent(Product $product): int
    {
        $tC = $product->target_containers->first(function ($t) {
            return $t->laundry_customer->cuid === $this->location->cuid;
        });

        $count = $tC->pivot->target_container_content;

        return $count;
    }
}
