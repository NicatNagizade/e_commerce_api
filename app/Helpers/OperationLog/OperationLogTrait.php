<?php

namespace App\Helpers\OperationLog;

use Illuminate\Support\Facades\DB;

trait OperationLogTrait
{
    public $operation_log_table = 'operation_logs';

    public function createLog(string $type = 'created')
    {
        DB::table($this->operation_log_table)->insert([
            'user_id' => auth()->id() ?: 0,
            'table_name' => $this->getTable(),
            'related_id' => $this->getKey(),
            'type' => $type,
            'old_data' => ($type === 'created') ? null : json_encode($this->original),
            'date' => now()
        ]);
    }
}
