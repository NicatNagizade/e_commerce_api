<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Color;

class ColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $colors = Color::paginate(10);
        return $this->sendSuccess($colors);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:colors,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $color = new Color;
        $color->name = request('name');
        $color->save();
        $color->createLog();
        return $this->sendSuccess(['inserted_id'=>$color->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:colors,name,'.$id
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $color = Color::findOrFail($id);
        $color->createLog('updated');
        $color->name = request('name');
        $color->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $color = Color::findOrFail($id);
        $color->createLog('deleted');
        $color->delete();
        return $this->sendSuccess();
    }
}
