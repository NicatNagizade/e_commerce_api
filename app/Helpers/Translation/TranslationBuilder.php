<?php

namespace App\Helpers\Translation;

use Illuminate\Database\Eloquent\Builder;

class TranslationBuilder extends Builder
{
    public function __construct($builder)
    {
        parent::__construct($builder);
    }

    public function getLanguage()
    {
        return $this->model->getLanguage();
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $args = func_get_args();
        $args[0] = $this->model->changeWhere($args[0]);
        return parent::where(...$args);
    }


    public function select()
    {
        $args = func_get_args();
        $args = is_array($args[0]) ? $args[0] : $args;
        $args = $this->model->changeSelect($args);
        return parent::select(...$args);
    }
}
