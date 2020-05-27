<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Metro;

class MetroController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $metros = Metro::with('region')->paginate(10);
        return $this->sendSuccess($metros);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:metros,name',
            'region_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $metro = new Metro;
        $metro->name = request('name');
        $metro->region_id = request('region_id');
        $metro->save();
        $metro->createLog();
        return $this->sendSuccess(['inserted_id'=>$metro->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:metros,name,'.$id,
            'region_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $metro = Metro::findOrFail($id);
        $metro->createLog('updated');
        $metro->name = request('name');
        $metro->region_id = request('region_id');
        $metro->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $metro = Metro::findOrFail($id);
        $metro->createLog('deleted');
        $metro->delete();
        return $this->sendSuccess();
    }
}
