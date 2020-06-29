<?php

namespace App\Helpers\OperationLog;

use Illuminate\Support\Facades\DB;

trait OperationLogTrait
{
    public static function operationLogAuto(): bool
    {
        return static::$operation_log_auto ?? true;
    }

    public function operationLogTable(): string
    {
        return static::$operation_log_table ?? 'operation_logs';
    }

    public function operationLogOnlyAuth()
    {
        return static::$operation_log_only_auth ?? true;
    }

    public function createLog(string $type = 'created', bool $old_data = true): void
    {
        $checkAuth = (!$this->operationLogOnlyAuth() || auth()->check());
        if ($checkAuth || !$this->operationLogAuto()) {
            DB::table($this->operationLogTable())->insert([
                'user_id' => auth()->id() ?: null,
                'table_name' => $this->getTable(),
                'related_id' => $this->getKey(),
                'type' => $type,
                'old_data' => ($old_data && $type !== 'created') ? json_encode($this->original) : null,
                'date' => now()
            ]);
        }
    }

    public static function bootOperationLogTrait(): void
    {
        if (static::operationLogAuto()) {
            self::created(function ($model) {
                $model->createLog();
            });
            self::updating(function ($model) {
                $model->createLog('updated');
            });
            self::deleting(function ($model) {
                $model->createLog('deleted');
            });
        }
    }
}
