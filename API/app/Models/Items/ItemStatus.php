<?php

namespace Cintas\Models\Items;

use Carbon\Carbon;
use Cintas\Models\AbstractModel;
use Cintas\Models\Pivot\ItemScanAction;
use Cintas\Models\Polymorphism\ItemStatusContract;
use Illuminate\Database\Query\Builder;

class ItemStatus extends AbstractModel implements ItemStatusContract
{

    protected $with = ['status_type'];

    protected $appends = ['status_text'];

    protected static function boot()
    {

        parent::boot();

        static::created(function ($model) {
            $item = $model->item()->withTrashed()->first(); // Required to get soft deleted items, too. Otherwise, deleted status cannot be set!
            $item->last_status()->associate($model);
            $item->save();
        });

    }

    public function getLabelAttribute($value)
    {
        return $this->status_text ?? parent::getLabelAttribute($value); // TODO: Change the autogenerated stub
    }


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function item_scan_action()
    {
        return $this->hasOne(ItemScanAction::class, 'new_status_id');
    }

    public function getStatusTextAttribute()
    {
        if (str_contains($this->status_type->status_text, '%s')) {
            $text = $this->status_type->status_text;
            $string = sprintf($text, $this->location->label);
        } else {
            $string = $this->status_type->status_text;
        }
        return $string;
    }

    public function location()
    {
        return $this->morphTo('location');
    }

    public function status_type()
    {
        return $this->belongsTo(ItemStatusType::class, 'item_status_type_id');
    }

    /**
     * @param Builder $query
     * @param Carbon $timestamp
     * @return Builder
     */
    public function scopeAtTimestamp($query, $timestamp)
    {
        return $query->where('created_at', '<=', $timestamp->timezone('UTC'))
            ->latest('created_at');
    }

}
