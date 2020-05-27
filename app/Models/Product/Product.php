<?php

namespace App\Models\Product;

use App\Helpers\FileHelper;
use App\Models\Msk\Color;
use App\Models\Msk\Size;
use App\Models\BaseModel;
use Carbon\Carbon;

class Product extends BaseModel
{
    public $timestamps = true;

    public $translation_table = 'translation_products';
    public $translation_many_table_name = false;
    public $translation_columns = ['name', 'description', 'content'];
    public const IMAGE_PATH = 'images/product/images/';
    public const SUB_IMAGE_PATH = 'images/product/sub_images/';

    protected $casts = [
        'price' => 'float',
    ];

    public function getImageAttribute($value)
    {
        return config('app.url') . '/'. static::IMAGE_PATH . $value;
    }

    public function getImageNameAttribute()
    {
        return str_replace(config('app.url') . '/'. static::IMAGE_PATH, '', $this->image);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_sizes');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function discount()
    {
        return $this->hasOne(ProductDiscount::class);
    }

    public function addSubImages($images): void
    {
        if (is_array($images)) {
            foreach ($images as $sub_image) {
                $image = new FileHelper($sub_image);
                $product_image = new ProductImage;
                $product_image->product_id = $this->id;
                $product_image->image = $image->getName();
                $product_image->save();
                $image->save(static::SUB_IMAGE_PATH);
            }
        }
    }

    public function addDiscount($price, $start, $end)
    {
        $end_date = Carbon::parse($end);
        $start_date = $start ? Carbon::parse($start) : now();
        $product_discount = new ProductDiscount;
        $product_discount->product_id = $this->id;
        $product_discount->start = $start_date;
        $product_discount->end = $end_date;
        $product_discount->price = $price;
        $product_discount->save();
    }

    public function deleteImages()
    {
        $this->deleteImage();
        $this->deleteSubImages();
    }

    public function deleteImage()
    {
        if ($this->image_name) {
            @unlink(public_path(static::IMAGE_PATH . $this->image_name));
        }
    }

    public function deleteSubImages()
    {
        foreach ($this->images as $sub_image) {
            if ($sub_image->image_name) {
                @unlink(public_path(static::SUB_IMAGE_PATH . $sub_image->image_name));
            }
        }
    }
}
