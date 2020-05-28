<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\TypeOfDelivery;

class TypeOfDeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $type_of_deliveries = TypeOfDelivery::paginate(10);
        return $this->sendSuccess($type_of_deliveries);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:type_of_deliveries,name',
            'content' => 'required|string',
            'price'=>'required|numeric|between:0.01,999999.99'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $type_of_delivery = new TypeOfDelivery;
        $type_of_delivery->name = request('name');
        $type_of_delivery->content = request('content');
        $type_of_delivery->price = request('price');
        $type_of_delivery->save();
        $type_of_delivery->createLog();
        return $this->sendSuccess(['inserted_id'=>$type_of_delivery->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:type_of_deliveries,name,'.$id,
            'content' => 'required|string',
            'price'=>'required|numeric|between:0.01,999999.99'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $type_of_delivery = TypeOfDelivery::findOrFail($id);
        $type_of_delivery->createLog('updated');
        $type_of_delivery->name = request('name');
        $type_of_delivery->content = request('content');
        $type_of_delivery->price = request('price');
        $type_of_delivery->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $type_of_delivery = TypeOfDelivery::findOrFail($id);
        $type_of_delivery->createLog('deleted');
        $type_of_delivery->delete();
        return $this->sendSuccess();
    }
}