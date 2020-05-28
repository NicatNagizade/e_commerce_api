<?php

namespace App\Http\Controllers\Dev;

use App\Helpers\Translation\TranslationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class TranslationController extends Controller
{
    private $msk_names = [
        'colors', 'categories', 'product_types',
        'sub_product_types','availability_of_products','countries',
        'coupons','currencies','designs','manufacturers','metros',
        'regions','sizes','sub_categories','tags','type_of_deliveries',
        'user_notifications'
        ];

    private $products_names = ['products'];

    public function getAllTranslations($table)
    {
        $model_name = $this->getModel($table);
        if($model_name === ''){
            return $this->sendError();
        }
        $translation = new TranslationHelper(new $model_name);
        $res = $translation->allTranslations()->paginate(10);
        return $this->sendSuccess($res);
    }

    public function updateTranslation($table, $id)
    {
        $model_name = $this->getModel($table);
        if($model_name === ''){
            return $this->sendError();
        }
        $model = new TranslationHelper(new $model_name, $id);
        $model->updateTranslation();
        return $this->sendSuccess();
    }

    public function getModel($table)
    {
        $table_camel = ucfirst(Str::camel(Str::singular($table)));
        if(in_array($table, $this->products_names)){
            return 'App\Models\Product\\'.$table_camel;
        }
        if(in_array($table, $this->msk_names)){
            return 'App\Models\Msk\\'.$table_camel;
        }
        return '';
    }
}
