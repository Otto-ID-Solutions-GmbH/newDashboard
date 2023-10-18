<?php

namespace Cintas\Models\Pivot;

use Cintas\Models\Items\ItemStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SkippedItemScanAction extends Pivot
{
    public function old_status()
    {
        return $this->belongsTo(ItemStatus::class, 'old_status_id');
    }

    public function new_status()
    {
        return $this->belongsTo(ItemStatus::class, 'old_status_id');
    }
}
