<?php

namespace App\Helpers\Translation;

trait TranslationTrait
{
    public $translation_table = 'translations';
    public $translation_columns = ['name'];
    public $translation_many_related_name = true;
    public $translation_many_table_name = true;
    public $translation_auto = false;
    private $translation_start = false;
    private $translation_helper;

    // begin translation
    public static function t(array $columns = [])
    {
        $model = new static;
        if (TranslationHelper::getLanguage() !== '') {
            if($columns){
                $model->translation_columns = $columns;
            }
            $model->translation_helper = new TranslationHelper($model);
            $model->translation_start = true;
            $query = $model->newQuery();
            $model->translation_helper->translation($query);
            return $query;
        }
        return $model->newQuery();
    }

    public function newEloquentBuilder($builder)
    {
        if ($this->translation_start) {
            return new TranslationBuilder($builder, $this->translation_helper);
        }
        return parent::newEloquentBuilder($builder);
    }

    public function newQuery()
    {
        if($this->translation_auto && !$this->translation_start){
            if(TranslationHelper::getLanguage() !== ''){
                $this->translation_helper = new TranslationHelper($this);
                $this->translation_start = true;
                $query = parent::newQuery();
                $this->translation_helper->translation($query);
                return $query;
            }
        }
        return parent::newQuery();
    }
}
