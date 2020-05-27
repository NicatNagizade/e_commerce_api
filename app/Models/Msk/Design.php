<?php

namespace App\Models\Msk;

use App\Models\BaseModel;

class Design extends BaseModel
{

    public function getImageAttribute($value)
    {
        return config('app.url') . '/images/design/' . $value;
    }

    public function getImageNameAttribute()
    {
        return str_replace(config('app.url') . '/images/design/', '', $this->image);
    }
}
