<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;

class DocController extends Controller
{
    public function loginDev()
    {
        if(request('username') === 'dev' && request('password') === 'password123'){
            session()->put('dev_login', true);
            return redirect('/dev/doc');
        }
        return redirect()->back();
    }
    public function index()
    {
        if(session('dev_login')){
            return view('dev/documentation');
        }
        return redirect('/dev');
    }

    public function getJsonData()
    {
        if(session('dev_login')){
            return file_get_contents(storage_path('documentation.json'));
        }
    }
}
