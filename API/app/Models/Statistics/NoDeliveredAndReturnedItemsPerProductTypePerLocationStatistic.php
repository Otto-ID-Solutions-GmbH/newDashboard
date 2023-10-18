<?php

namespace Cintas\Models\Statistics;

use Cintas\Models\AbstractModel;
use Cintas\Models\Items\ProductType;

class NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic extends AbstractModel
{
    protected $table = 'delivered_returned_items_statistics';

    protected $dates = [
        'date',
    ];

    public function product_type()
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function location()
    {
        return $this->morphTo('location');
    }

}
