<?php

namespace App\Models\Msk;

use App\Models\BaseModel;

class Metro extends BaseModel
{
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
