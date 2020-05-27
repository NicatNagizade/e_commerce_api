<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Country;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $countries = Country::paginate(10);
        return $this->sendSuccess($countries);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:countries,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $country = new Country;
        $country->name = request('name');
        $country->save();
        $country->createLog();
        return $this->sendSuccess(['inserted_id'=>$country->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:countries,name,'.$id
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $country = Country::findOrFail($id);
        $country->createLog('updated');
        $country->name = request('name');
        $country->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->createLog('deleted');
        $country->delete();
        return $this->sendSuccess();
    }
}
