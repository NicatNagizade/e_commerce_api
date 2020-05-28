<?php

namespace App\Helpers\Translation;

use Illuminate\Database\Eloquent\Builder;

class TranslationBuilder extends Builder
{
    private $helper;
    
    public function __construct($builder, $helper)
    {
        $this->helper = $helper;
        parent::__construct($builder);
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $args = func_get_args();
        $args[0] = $this->helper->changeWhere($args[0]);
        return parent::where(...$args);
    }

    public function select()
    {
        $args = func_get_args();
        $args = is_array($args[0]) ? $args[0] : $args;
        $args = $this->helper->changeSelect($args);
        return parent::select(...$args);
    }
}
