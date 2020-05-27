<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Region;

class RegionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $regions = Region::with('country')->paginate(10);
        return $this->sendSuccess($regions);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:regions,name',
            'country_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $region = new Region;
        $region->name = request('name');
        $region->country_id = request('country_id');
        $region->save();
        $region->createLog();
        return $this->sendSuccess(['inserted_id'=>$region->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:regions,name,'.$id,
            'country_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $region = Region::findOrFail($id);
        $region->createLog('updated');
        $region->name = request('name');
        $region->country_id = request('country_id');
        $region->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        $region->createLog('deleted');
        $region->delete();
        return $this->sendSuccess();
    }
}