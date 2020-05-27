<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Size;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $sizes = Size::paginate(10);
        return $this->sendSuccess($sizes);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:sizes,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $size = new Size;
        $size->name = request('name');
        $size->save();
        $size->createLog();
        return $this->sendSuccess(['inserted_id'=>$size->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:sizes,name,'.$id,
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $size = Size::findOrFail($id);
        $size->createLog('updated');
        $size->name = request('name');
        $size->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $size = Size::findOrFail($id);
        $size->createLog('deleted');
        $size->delete();
        return $this->sendSuccess();
    }
}