<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OperationLog extends Model
{
    public $timestamps = false;
    protected $appends = ['now_data'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOldDataAttribute($value)
    {
        return json_decode($value);
    }

    public function getNowDataAttribute()
    {
        return DB::table($this->table_name)->where('id',$this->related_id)->first();
    }
}
