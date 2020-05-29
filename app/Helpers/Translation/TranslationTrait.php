<?php

namespace App\Helpers\Translation;

trait TranslationTrait
{
    public $translation_table = 'translations';
    public $translation_columns = ['name'];
    public $translation_many_related_name = true;
    public $translation_many_table_name = true;
    public $translation_auto = false;
    private $translation_helper;

    public function translationBeginCheck(): bool
    {
        if ($this->translation_auto === true) {
            if (TranslationHelper::getLanguage() !== '') {
                return true;
            }
        }
        return false;
    }

    // begin translation
    public static function t(array $columns = [])
    {
        $model = new static;
        $model->translation_auto = true;
        if ($columns) {
            $model->translation_columns = $columns;
        }
        return $model->newQuery();
    }

    public function newEloquentBuilder($builder)
    {
        if ($this->translationBeginCheck()) {
            return new TranslationBuilder($builder, $this->translation_helper);
        }
        return parent::newEloquentBuilder($builder);
    }

    public function newQuery()
    {
        if ($this->translationBeginCheck()){
            $this->translation_helper = new TranslationHelper($this);
            $query = parent::newQuery();
            $this->translation_helper->translation($query);
            return $query;
        }
        return parent::newQuery();
    }
}
