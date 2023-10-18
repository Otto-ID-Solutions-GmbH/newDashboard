<?php

namespace Cintas\Models\Items;

use Carbon\Carbon;
use Cintas\Models\AbstractModel;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Facility\Bundle;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Pivot\ItemScanAction;
use Cintas\Models\Pivot\SkippedItemScanAction;
use Cintas\Models\Polymorphism\HasStatusContract;
use Cintas\Models\Polymorphism\IdentifiableContract;
use Cintas\Models\Polymorphism\IdentifiableTrait;
use Cintas\Models\Polymorphism\LocatableContract;
use Cintas\Models\Polymorphism\LocatableTrait;
use Cintas\Models\Polymorphism\ScanableContract;
use Cintas\Models\Polymorphism\ScanableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends AbstractModel implements IdentifiableContract, LocatableContract, ScanableContract, HasStatusContract
{

    use SoftDeletes;

    use IdentifiableTrait, ScanableTrait, LocatableTrait;

    //protected $with = ['last_status', 'product', 'rfid_tags'];

    /**
     * Item constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->append('remaining_lifetime');
    }

    protected static function boot()
    {
        parent::boot();
        static::created(function ($model) {

            // If the deleted_at attribute is already set, sort out the item
            if ($model->deleted_at !== null) {
                $stat = new ItemStatus();
                $stat->created_at = $model->deleted_at;
                $stat->status_type()->associate(ItemStatusType::findByName('SortedOutStatus'));
                $model->pushStatus($stat);
                return;
            }

            // Otherwise, initialize the status of the item as 'new'
            if (!$model->last_status_id) {
                $stat = new ItemStatus();
                $stat->created_at = $model->created_at;
                $stat->status_type()->associate(ItemStatusType::findByName('NewStatus'));
                $model->pushStatus($stat);
            }
        });

        // When deleting an item, set the last status to 'deleted'
        static::deleted(function ($model) {
            $stat = new ItemStatus();
            $stat->created_at = $model->deleted_at;
            $stat->status_type()->associate(ItemStatusType::findByName('SortedOutStatus'));
            $model->pushStatus($stat);
        });


    }


    public function getLabelAttribute($value)
    {
        return $this->product->label . ', #' . $this->rfid_tags->first()->label;

    }

    public function getRemainingLifetimeAttribute()
    {

        if (!($this->product->expected_lifetime ?? null)) {
            return null;
        }

        return $this->product->expected_lifetime - ($this->cycle_count ?? 0);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }

    public function last_status()
    {
        return $this->belongsTo(ItemStatus::class, 'last_status_id');
    }

    public function status_history()
    {
        return $this->hasMany(ItemStatus::class, 'item_id');
    }

    public function scan_actions()
    {
        return $this
            ->belongsToMany(ScanAction::class, 'item_scan_action')
            ->using(ItemScanAction::class)
            ->withPivot(['read_at', 'antenna']);
    }

    public function skipped_in_scan_actions()
    {
        return $this
            ->belongsToMany(ScanAction::class, 'skipped_item_scan_action')
            ->using(SkippedItemScanAction::class)
            ->withPivot(['read_at', 'antenna']);
    }

    /**
     * Pushes a new status to the status history and reloads the last status relation to reflect the status change.
     * Don't save new statuses to the status_history relation directly!
     *
     * @param mixed $status the status to push to the history
     * @return false|ItemStatus the new status pushed to the history
     */
    public function pushStatus($status)
    {
        $res = $this->status_history()->save($status);
        //$this->load('last_status');
        $this->refresh(); // REQUIRED so Laravel reloads the last status relation from the database. Unset relation did not work!
        return $res;
    }

    public function getLocationAttribute($value = null)
    {
        return $this->last_status->location;
    }

    public function setLocationAttribute($value, $updateLastStatus = false)
    {

        if ($value instanceof Facility) {
            $statusType = 'AtFacilityStatus';
        } else if ($value instanceof LaundryCustomer) {
            $statusType = 'AtCustomerStatus';
        } else {
            $statusType = 'UnknownStatus';
        }

        if ($updateLastStatus) {
            $this->last_status->location()->associate($value);
            return;
        }

        $status = new ItemStatus();
        $status->status_type()->associate(ItemStatusType::findByName($statusType));

        if ($value) {
            $status->location()->associate($value);
        }

        $this->pushStatus($status);

    }

    /**
     * Returns only items that are outdated
     * @param $query
     * @param int $minDays the number of days that the last status has bee passed to be defined as outdated
     * @param Carbon $referenceDate
     * @return mixed
     */
    public function scopeOnlyOutdated($query, $minDays = null, $referenceDate = null, $maxDays = null)
    {
        if (!$referenceDate) {
            $referenceDate = Carbon::now();
        }

        if ($minDays === null) {
            $minDays = config('cintas.process.outdated_limit');
        }


        $checkDate = clone $referenceDate;
        $startDate = clone $referenceDate;

        $checkDate = $checkDate->subDays($minDays)->timezone('UTC');
        $startDate = $startDate->subDays($maxDays)->timezone('UTC');

        if (!AbstractModel::isJoined($query, 'item_statuses')) {
            $query = $query->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid');
        }

        $query = $query
            ->where('item_statuses.created_at', '<', $checkDate);

        if ($maxDays != null) {
            $query = $query->where('item_statuses.created_at', '>=', $startDate);
        }

        return $query;
    }

    /**
     * Returns only items that are not outdated
     * @param $query
     * @param int $limit the number of days that the last status has bee passed to be defined as outdated
     * @param Carbon $referenceDate
     * @return mixed
     */
    public function scopeOnlyCirculating($query, $limit = null, $referenceDate = null)
    {
        if (!$referenceDate) {
            $referenceDate = Carbon::now();
        }

        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $checkDate = clone $referenceDate;
        $checkDate = $checkDate->subDays($limit)->timezone('UTC');

        if (!AbstractModel::isJoined($query, 'item_statuses')) {
            $query = $query->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid');
        }

        return $query
            ->where('cycle_count', '>', 0)
            ->where('item_statuses.created_at', '>=', $checkDate);
    }

    public function scopeOnlyAtLocationType($query, $locationType, $includeUnknown = false)
    {

        if ($includeUnknown) {
            if (!AbstractModel::isJoined($query, 'item_statuses')) {
                $query = $query->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid');
            }
            return $query
                ->where(function ($query) use ($locationType) {
                    return $query
                        ->where('item_statuses.location_type', '=', $locationType)
                        ->orWhereNull('item_statuses.location_id');
                });
        } else {
            if (!AbstractModel::isJoined($query, 'item_statuses')) {
                $query = $query->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid');
            }
            return $query
                ->where('item_statuses.location_type', '=', $locationType);
        }
    }

}
