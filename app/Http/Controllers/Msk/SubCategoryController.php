<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\SubCategory;

class SubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $sub_categories = SubCategory::paginate(10);
        return $this->sendSuccess($sub_categories);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:sub_categories,name',
            'category_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $sub_category = new SubCategory;
        $sub_category->name = request('name');
        $sub_category->category_id = request('category_id');
        $sub_category->save();
        $sub_category->createLog();
        return $this->sendSuccess(['inserted_id'=>$sub_category->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:sub_categories,name,'.$id,
            'category_id' => 'required|integer'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $sub_category = SubCategory::findOrFail($id);
        $sub_category->createLog('updated');
        $sub_category->name = request('name');
        $sub_category->category_id = request('category_id');
        $sub_category->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $sub_category = SubCategory::findOrFail($id);
        $sub_category->createLog('deleted');
        $sub_category->delete();
        return $this->sendSuccess();
    }
}
