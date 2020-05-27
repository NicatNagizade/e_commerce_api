<?php

namespace App\Models;

use App\Helpers\OperationLog\OperationLogTrait;
use App\Helpers\Translation\TranslationTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use OperationLogTrait, TranslationTrait;
    public $timestamps = false;
}