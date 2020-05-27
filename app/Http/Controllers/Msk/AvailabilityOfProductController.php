<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\AvailabilityOfProduct;

class AvailabilityOfProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $availability_of_products = AvailabilityOfProduct::paginate(10);
        return $this->sendSuccess($availability_of_products);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:availability_of_products,name',
            'icon' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $availability_of_product = new AvailabilityOfProduct;
        $availability_of_product->name = request('name');
        $availability_of_product->icon = request('icon');
        $availability_of_product->save();
        $availability_of_product->createLog();
        return $this->sendSuccess(['inserted_id'=>$availability_of_product->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:availability_of_products,name,'.$id,
            'icon' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $availability_of_product = AvailabilityOfProduct::findOrFail($id);
        $availability_of_product->createLog('updated');
        $availability_of_product->name = request('name');
        $availability_of_product->icon = request('icon');
        $availability_of_product->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $availability_of_product = AvailabilityOfProduct::findOrFail($id);
        $availability_of_product->createLog('deleted');
        $availability_of_product->delete();
        return $this->sendSuccess();
    }
}
