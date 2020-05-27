<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\UserNotification;

class UserNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $user_notifications = UserNotification::paginate(10);
        return $this->sendSuccess($user_notifications);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'content' => 'required|string|unique:user_notifications,content',
            'type' => 'nullable|in:info,success,danger,warning',
            'icon'=>'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $user_notification = new UserNotification;
        $user_notification->content = request('content');
        $user_notification->icon = request('icon');
        $user_notification->type = request('type') ?: 'info';
        $user_notification->save();
        $user_notification->createLog();
        return $this->sendSuccess(['inserted_id'=>$user_notification->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'content' => 'required|string|unique:user_notifications,content,'.$id,
            'type' => 'nullable|in:info,success,danger,warning',
            'icon'=>'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $user_notification = UserNotification::findOrFail($id);
        $user_notification->createLog('updated');
        $user_notification->content = request('content');
        $user_notification->icon = request('icon');
        $user_notification->type = request('type') ?: 'info';
        $user_notification->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $user_notification = UserNotification::findOrFail($id);
        $user_notification->createLog('deleted');
        $user_notification->delete();
        return $this->sendSuccess();
    }
}
