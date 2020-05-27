<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $categories = Category::paginate(10);
        return $this->sendSuccess($categories);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:categories,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $category = new Category;
        $category->name = request('name');
        $category->save();
        $category->createLog();
        return $this->sendSuccess(['inserted_id'=>$category->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:categories,name,'.$id
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $category = Category::findOrFail($id);
        $category->createLog('updated');
        $category->name = request('name');
        $category->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->createLog('deleted');
        $category->delete();
        return $this->sendSuccess();
    }
}