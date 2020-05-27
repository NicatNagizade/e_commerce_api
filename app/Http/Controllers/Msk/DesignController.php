<?php

namespace App\Http\Controllers\Msk;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Models\Msk\Design;
use Illuminate\Support\Facades\DB;

class DesignController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $designs = Design::paginate(10);
        return $this->sendSuccess($designs);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:designs,name',
            'image'=>'required|file|image|max:10000',
            'price'=>'required|numeric|between:0.01,999999.99',
            'sub_product_type_id' => 'nullable|integer|exists:sub_product_types,id'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        DB::beginTransaction();
        try {
            $image = new FileHelper(request('image'));
            $design = new Design;
            $design->name = request('name');
            $design->price = request('price');
            $design->sub_product_type_id = request('sub_product_type_id');
            $design->image = $image->getName();
            $design->save();
            $design->createLog();
            $image->save('images/design');
            DB::commit();
            return $this->sendSuccess(['inserted_id'=>$design->id]);

        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:designs,name,'.$id,
            'image'=>'nullable|file|image|max:10000',
            'price'=>'required|numeric|between:0.01,999999.99',
            'sub_product_type_id' => 'nullable|integer|exists:sub_product_types,id'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $image = new FileHelper(request('image'));
        DB::beginTransaction();
        try {
            $design = Design::findOrFail($id);
            $design->createLog('updated');
            $design->name = request('name');
            $design->price = request('price');
            $design->sub_product_type_id = request('sub_product_type_id');
            if($image->has()){
                @unlink(public_path('images/design/'.$design->image_name));
                $design->image = $image->getName();
            }
            $design->save();
            $image->save('images/design');
            DB::commit();
            return $this->sendSuccess();

        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    public function destroy($id)
    {
        $design = Design::findOrFail($id);
        if($design->image){
            @unlink('images/design/'.$design->image_name);
        }
        $design->createLog('deleted');
        $design->delete();
        return $this->sendSuccess();
    }
}
