<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\SubProductType;

class SubProductTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $sub_product_types = SubProductType::paginate(10);
        return $this->sendSuccess($sub_product_types);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:sub_product_types,name',
            'product_type_id' => 'required|integer|exists:product_types,id'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $sub_product_type = new SubProductType;
        $sub_product_type->name = request('name');
        $sub_product_type->product_type_id = request('product_type_id');
        $sub_product_type->save();
        $sub_product_type->createLog();
        return $this->sendSuccess(['inserted_id'=>$sub_product_type->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:sub_product_types,name,'.$id,
            'product_type_id' => 'required|integer|exists:product_types,id'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $sub_product_type = SubProductType::findOrFail($id);
        $sub_product_type->createLog('updated');
        $sub_product_type->name = request('name');
        $sub_product_type->product_type_id = request('product_type_id');
        $sub_product_type->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $sub_product_type = SubProductType::findOrFail($id);
        $sub_product_type->createLog('deleted');
        $sub_product_type->delete();
        return $this->sendSuccess();
    }
}
