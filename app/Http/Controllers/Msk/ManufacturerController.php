<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Manufacturer;

class ManufacturerController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $manufacturers = Manufacturer::paginate(10);
        return $this->sendSuccess($manufacturers);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:manufacturers,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $manufacturer = new Manufacturer;
        $manufacturer->name = request('name');
        $manufacturer->save();
        $manufacturer->createLog();
        return $this->sendSuccess(['inserted_id'=>$manufacturer->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:manufacturers,name,'.$id,
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->createLog('updated');
        $manufacturer->name = request('name');
        $manufacturer->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->createLog('deleted');
        $manufacturer->delete();
        return $this->sendSuccess();
    }
}