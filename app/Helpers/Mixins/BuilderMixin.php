<?php

namespace App\Helpers\Mixins;

use Illuminate\Support\Facades\DB;

class BuilderMixin
{
    public function search()
    {
        return function ($column, $value) {
            return $this->where(DB::raw('LOWER(' . $column . ')'), 'LIKE', '%' . strtolower($value) . '%');
        };
    }
}