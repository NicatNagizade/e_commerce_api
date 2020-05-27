<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Tag;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $tags = Tag::paginate(10);
        return $this->sendSuccess($tags);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:tags,name'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $tag = new Tag;
        $tag->name = request('name');
        $tag->save();
        $tag->createLog();
        return $this->sendSuccess(['inserted_id'=>$tag->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:tags,name,'.$id
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $tag = Tag::findOrFail($id);
        $tag->createLog('updated');
        $tag->name = request('name');
        $tag->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->createLog('deleted');
        $tag->delete();
        return $this->sendSuccess();
    }
}
