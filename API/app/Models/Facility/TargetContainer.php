<?php

namespace Cintas\Models\Facility;

use Cintas\Models\AbstractModel;
use Cintas\Models\Items\ProductType;

class TargetContainer extends AbstractModel
{
    //
    public function laundry_customer()
    {
        return $this->belongsTo(LaundryCustomer::class, 'laundry_customer_id');
    }

    public function product_types()
    {
        return $this->belongsToMany(ProductType::class)->withPivot(['target_container_content']);
    }
}
