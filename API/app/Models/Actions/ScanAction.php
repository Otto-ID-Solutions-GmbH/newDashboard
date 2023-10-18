<?php

namespace Cintas\Models\Actions;

use Carbon\Carbon;
use Cintas\Models\AbstractModel;
use Cintas\Models\Facility\Reader;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Cintas\Models\Pivot\ItemScanAction;
use Cintas\Models\Pivot\SkippedItemScanAction;

class ScanAction extends AbstractModel
{

    protected $with = ['type'];

    protected $appends = ['items_count', 'unknown_tags_count', 'skipped_items_count'];

    public function getLabelAttribute($value)
    {
        $date = new Carbon($this->created_at); //TODO: Convert to server local timezone
        return $this->type->label . ' ' . $date->toIso8601String() . ' (' . $this->cuid . ')';
    }


    public function type()
    {
        return $this->belongsTo(ScanActionType::class, 'scan_type_id');
    }

    public function out_scan_action()
    {
        return $this->hasOne(OutScanAction::class);
    }

    public function reader()
    {
        return $this->belongsTo(Reader::class, 'reader_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class)
            ->using(ItemScanAction::class)
            ->withPivot(['read_at', 'antenna', 'find_type']);
    }

    public function skipped_items()
    {
        return $this->belongsToMany(Item::class, 'skipped_item_scan_action')
            ->using(SkippedItemScanAction::class)
            ->withPivot(['read_at', 'antenna']);
    }

    public function unknown_rfid_tags()
    {
        return $this->morphedByMany(RFIDTag::class, 'identifier', 'identifier_scan_action');
    }

    public function scopeOnlyOfType($query, $typeName)
    {
        return $query->whereHas('type', function ($query) use ($typeName) {
            return $query->where('name', '=', $typeName);
        });
    }

    public function scopeOrOnlyOfType($query, $typeName)
    {
        return $query->orWhereHas('type', function ($query) use ($typeName) {
            return $query->where('name', '=', $typeName);
        });
    }

    public function getItemsCountAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }

        return $this->items()->count();
    }

    public function getSkippedItemsCountAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        return $this->skipped_items()->count();
    }

    public function getUnknownTagsCountAttribute()
    {
        return $this->unknown_rfid_tags()->count();
    }

}
