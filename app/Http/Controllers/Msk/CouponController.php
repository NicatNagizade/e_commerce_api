<?php

namespace App\Http\Controllers\Msk;

use App\Http\Controllers\Controller;
use App\Models\Msk\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin')->except('index');
    }

    public function index()
    {
        $coupons = Coupon::paginate(10);
        return $this->sendSuccess($coupons);
    }

    public function store()
    {
        $validator = validator(request()->all(),[
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric|between:0.01,999999.99',
            'percent' => 'nullable|boolean',
            'type' => 'nullable|in:both,product,delivery',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $start_date = request('start_date') 
            ? Carbon::parse(request('start_date'))->format('Y-m-d')
            : now()->format('Y-m-d');
        $end_date = request('end_date') 
            ? Carbon::parse(request('end_date'))->format('Y-m-d') : null;
        $coupon = new Coupon;
        $coupon->code = request('code');
        $coupon->discount = request('discount');
        $coupon->percent = request('percent') ?: false;
        $coupon->type = request('type') ?: 'both';
        $coupon->start = $start_date;
        $coupon->end = $end_date;
        $coupon->save();
        $coupon->createLog();
        return $this->sendSuccess(['inserted_id'=>$coupon->id]);
    }

    public function update($id)
    {
        $validator = validator(request()->all(),[
            'code' => 'required|string|unique:coupons,code,'.$id,
            'discount' => 'required|numeric|between:0.01,999999.99',
            'percent' => 'nullable|boolean',
            'type' => 'nullable|in:both,product,delivery',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        $start_date = request('start_date') 
            ? Carbon::parse(request('start_date'))->format('Y-m-d')
            : now()->format('Y-m-d');
        $end_date = request('end_date') 
            ? Carbon::parse(request('end_date'))->format('Y-m-d') : null;
        $coupon = Coupon::findOrFail($id);
        $coupon->createLog('updated');
        $coupon->code = request('code');
        $coupon->discount = request('discount');
        $coupon->percent = request('percent') ?: false;
        $coupon->type = request('type') ?: 'both';
        $coupon->start = $start_date;
        $coupon->end = $end_date;
        $coupon->save();
        return $this->sendSuccess();
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->createLog('deleted');
        $coupon->delete();
        return $this->sendSuccess();
    }
}
