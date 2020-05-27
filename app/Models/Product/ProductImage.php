<?php

namespace App\Models\Product;

use App\Models\BaseModel;

class ProductImage extends BaseModel
{
    public const IMAGE_PATH = 'images/product/sub_images/';

    public function getImageAttribute($value)
    {
        return config('app.url').'/'.static::IMAGE_PATH.$value;
    }
    
    public function getImageNameAttribute()
    {
        return str_replace(config('app.url') . '/'.static::IMAGE_PATH, '', $this->image);
    }
}
