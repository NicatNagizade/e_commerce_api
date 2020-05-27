<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $currencies = Currency::paginate(10);
        return $this->sendSuccess($currencies);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:currencies,name',
            'amount' => 'required|numeric|between:0.01,999999.99'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $currency = new Currency;
        $currency->name = request('name');
        $currency->amount = request('amount');
        $currency->save();
        $currency->createLog();
        return $this->sendSuccess(['inserted_id'=>$currency->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'name' => 'required|string|unique:currencies,name,'.$id,
            'amount' => 'required|numeric|between:0.01,999999.99'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $currency = Currency::findOrFail($id);
        $currency->createLog('updated');
        $currency->name = request('name');
        $currency->amount = request('amount');
        $currency->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->createLog('deleted');
        $currency->delete();
        return $this->sendSuccess();
    }
}