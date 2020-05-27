<?php

namespace App\Models\Msk;

use App\Models\BaseModel;

class Region extends BaseModel
{
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
