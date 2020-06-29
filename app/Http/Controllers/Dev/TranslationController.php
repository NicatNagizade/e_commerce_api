<?php

namespace App\Http\Controllers\Dev;

use App\Helpers\Translation\TranslationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class TranslationController extends Controller
{
    private $msk_names = [
        'color', 'category', 'product_type',
        'sub_product_type', 'availability_of_product', 'country',
        'coupon', 'currency', 'design', 'manufacturer', 'metro',
        'region', 'size', 'sub_category', 'tag', 'type_of_delivery',
        'user_notification'
    ];

    private $product_names = ['product'];

    public function getAllTranslations($table)
    {
        $validator = validator(request()->all(), [
            'key' => 'nullable|string',
            'value' => 'nullable|string',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $model_name = $this->getModel($table);
        if ($model_name === '') {
            return $this->sendError();
        }
        $translation = new TranslationHelper(new $model_name);
        if (
            request()->has('key')
            && !in_array(request('key'), $translation->getModel()->translationColumns())
        ) {
            return $this->sendError('Gonderdiyiniz key bu modelde yoxdur');
        }
        $data = $translation->allTranslations()
            ->when(request()->has('key') && request()->has('value'), function ($q) {
                $q->search(request('key'), request('value'));
            })
            ->paginate(10);
        $data = $translation->allTranslationToArray($data);
        return $this->sendSuccess($data);
    }

    public function updateTranslation($table, $id)
    {
        $model_name = $this->getModel($table);
        if ($model_name === '') {
            return $this->sendError();
        }
        $model = new TranslationHelper($model_name::findOrFail($id));
        $success_args = $model->updateTranslation();
        if (empty($success_args)) {
            return $this->sendError('Hec bir soz yazilmadi , sozleri duzgun formatda gonderdiyinizden emin olun');
        }
        return $this->sendSuccess($success_args);
    }

    public function getModel($table)
    {
        $table_camel = ucfirst(Str::camel($table));
        if (in_array($table, $this->product_names)) {
            return 'App\Models\Product\\' . $table_camel;
        }
        if (in_array($table, $this->msk_names)) {
            return 'App\Models\Msk\\' . $table_camel;
        }
        return '';
    }
}
