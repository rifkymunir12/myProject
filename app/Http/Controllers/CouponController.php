<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Coupon;
use App\Contracts\Response;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponCollection;
use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use App\Models\Role;
use Laravel\Passport\Passport;


class CouponController extends Controller
{
    public function index(Request $request){
        if (auth()->user()->hasRole('User')){
            return Response::abortForbidden();
        }

        $coupons = Coupon::paginate($request->show);

        return Response::json(new CouponCollection($coupons));
    }

    public function show(Coupon $coupon){
        if (auth()->user()->hasRole('User')){
            return Response::abortForbidden();
        }

        return Response::json(new CouponResource($coupon));
    }
    public function store(CouponRequest $request){
        //if (auth()->user()->getRoleNames()->first() !== 'User'){
        if (!auth()->user()->hasRole('Admin')){
                return Response::abortForbidden();
        }
            $data = $request->validated();
            
            // $newCoupon = Coupon::create([
            //     'name'           =>   $data['name'],    
            //     'code'           =>   $data['code'],
            //     'discount'       =>   $data['discount'],        
            //     'description'    =>   $data['description'],
            // ]); 

            $newCoupon = Coupon::create($data);

            return Response::json(new CouponResource($newCoupon));
    }
         

    public function update(CouponRequest $request, Coupon $coupon){
        //if (auth()->user()->getRoleNames()->first() !== 'User'){
        if (!auth()->user()->hasRole('Admin')){
                return Response::abortForbidden();
        }
            
            $data = $request->validated();
            $updatedCoupon = $coupon;

            // $updatedCoupon->update([
            //     'name'           =>   $data['name'] ?? $coupon->name,
            //     'code'           =>   $data['code'] ?? $coupon->code,
            //     'discount'       =>   $data['discount'] ?? $coupon->discount,
            //     'description'    =>   $data['description'] ?? $coupon->decription,
            // ]);
        
            $updatedCoupon->update($data);

            return Response::json(new CouponResource($updatedCoupon));
    }

    public function destroy(Coupon $coupon){
        //if (auth()->user()->getRoleNames()->first() !== 'User'){    
        if (!auth()->user()->hasRole('Admin')){
            return Response::abortForbidden();
        }
        $coupon->delete();
        return Response::noContent();
    }

}
