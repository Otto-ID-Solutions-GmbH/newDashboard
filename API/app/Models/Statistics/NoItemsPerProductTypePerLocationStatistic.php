<?php

namespace Cintas\Models\Statistics;

use Cintas\Models\AbstractModel;
use Cintas\Models\Items\ProductType;

class NoItemsPerProductTypePerLocationStatistic extends AbstractModel
{
    //
    protected $table = 'per_product_type_per_location_statistics';

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
