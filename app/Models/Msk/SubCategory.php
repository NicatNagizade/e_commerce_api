<?php

namespace App\Models\Msk;

use App\Models\BaseModel;

class SubCategory extends BaseModel
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
