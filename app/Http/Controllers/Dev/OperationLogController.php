<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use App\Models\OperationLog;

class OperationLogController extends Controller
{
    public function allLogsAndFilter()
    {
        $validator = validator(request()->all(), [
            'table_name' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'type' => 'nullable|in:created,deleted,updated',
            'user_id' => 'nullable|integer',
            'key' => 'nullable|string',
            'value' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $logs = OperationLog::with(['user:id,username'])
            ->when(request('table_name'), function ($q) {
                $q->where('table_name', request('table_name'));
            })
            ->when(request('related_id'), function ($q) {
                $q->where('related_id', request('related_id'));
            })
            ->when(request('user_id'), function ($q) {
                $q->where('user_id', request('user_id'));
            })
            ->when(request('type'), function ($q) {
                $q->where('type', request('type'));
            })
            ->when(request('value'), function ($q) {
                $key = request('key', 'name');
                $q->where('data->' . $key, request('value'));
            })
            ->paginate(20);

        return $logs;
    }
}
