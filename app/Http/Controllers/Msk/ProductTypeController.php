<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\ProductType;

class ProductTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $product_types = ProductType::paginate(10);
        return $this->sendSuccess($product_types);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:product_types,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $product_type = new ProductType;
        $product_type->name = request('name');
        $product_type->save();
        $product_type->createLog();
        return $this->sendSuccess(['inserted_id'=>$product_type->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:product_types,name,'.$id
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $product_type = ProductType::findOrFail($id);
        $product_type->createLog('updated');
        $product_type->name = request('name');
        $product_type->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $product_type = ProductType::findOrFail($id);
        $product_type->createLog('deleted');
        $product_type->delete();
        return $this->sendSuccess();
    }
}