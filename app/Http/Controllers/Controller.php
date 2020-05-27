<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSuccess($data = null)
    {
        return response()->json(['status' => true, 'data' => $data]);
    }
    public function sendError($errors = [], $message = null)
    {
        $count_args = func_num_args();
        if ($count_args === 0) {
            $message = 'Parametrlərdə xəta var';
        } else if ($count_args === 1 && is_string($errors)) {
            $message = (string) $errors;
            $errors = [];
        }
        return response()->json(['status' => false, 'errors' => $errors, 'message' => $message]);
    }
}
