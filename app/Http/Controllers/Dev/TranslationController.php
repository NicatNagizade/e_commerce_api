<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class TranslationController extends Controller
{
    private $msk_names = [
        'color', 'category', 'product_type',
        'sub_product_type','availability_of_product','country',
        'coupon','currency','design','manufacturer','metro',
        'region','size','sub_category','tag','type_of_delivery',
        'user_notification'
        ];

    private $products_names = ['product'];

    public function getAllTranslations($table)
    {
        $model_name = $this->getModel($table);
        if($model_name === ''){
            return $this->sendError();
        }
        $res = $model_name::allTranslations()->paginate(10);
        return $this->sendSuccess($res);
    }

    public function updateTranslation($table, $id)
    {
        $model_name = $this->getModel($table);
        if($model_name === ''){
            return $this->sendError();
        }
        $model = $model_name::findOrFail($id);
        $model->updateTranslation();
        return $this->sendSuccess();
    }

    public function getModel($table)
    {
        $table_camel = ucfirst(Str::camel($table));
        if(in_array($table, $this->products_names)){
            return 'App\Models\Product\\'.$table_camel;
        }
        if(in_array($table, $this->msk_names)){
            return 'App\Models\Msk\\'.$table_camel;
        }
        return '';
    }
}
