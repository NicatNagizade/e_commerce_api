<?php

namespace App\Models\Msk;

use App\Models\BaseModel;

class SubProductType extends BaseModel
{
    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }
}
