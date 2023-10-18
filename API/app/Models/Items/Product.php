<?php

namespace Cintas\Models\Items;

use Carbon\Carbon;
use Cintas\Models\AbstractModel;
use Cintas\Models\Facility\LaundryCustomer;

class Product extends AbstractModel
{

    //protected $appends = ['avg_cycle_count', 'avg_age_in_days', 'no_old_items', 'no_lost_items'];

    public function getLabelAttribute($value)
    {
        return $this->name;
    }

    public function getExpectedLifetimeAttribute($value)
    {
        if (!($this->attributes['expected_lifetime'] ?? null)) {
            return $this->product_type->expected_lifetime;
        } else {
            return $value;
        }
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function laundry_customers()
    {
        return $this->belongsToMany(LaundryCustomer::class);
    }

    public function getAvgCycleCountAttribute()
    {
        return $this->items()->onlyCirculating()->avg('cycle_count');
    }

    public function getAvgAgeInDaysAttribute($referenceDate = null)
    {
        if (!$referenceDate) {
            $referenceDate = Carbon::now();
        }

        $result = $this->items()->selectRaw('AVG(DATEDIFF(NOW(),items.created_at)) AS avg_datediff')->pluck('avg_datediff')->first();

        return $result;
    }

    public function getNoOldItemsAttribute($referenceDate = null, $timeInYears = 2)
    {
        if (!$referenceDate) {
            $referenceDate = Carbon::now();
        }

        $compDate = $referenceDate->subYears($timeInYears);

        return $this->items()
            ->onlyCirculating()
            ->where('items.created_at', '<=', $compDate)
            ->count();
    }

    public function getNoLostItemsAttribute($minDays = null, $referenceDate = null, $location = null, $maxDays = null)
    {

        if (!$minDays) {
            $minDays = config('cintas.process.outdated_limit');
        }

        $query = $this
            ->items()
            ->onlyOutdated($minDays, $referenceDate, $maxDays);

        if ($location && $location !== 'Unknown') {
            $query = $query->where('item_statuses.location_id', '=', $location->cuid);
        } elseif ($location && $location === 'Unknown') {
            $query = $query->whereNull('item_statuses.location_id');
        }

        return $query->count();
    }
}
