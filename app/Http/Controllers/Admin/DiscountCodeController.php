<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request){
        $discountCoupons = DiscountCoupon::latest();

        if ($request->filled('keyword')) {
            $discountCoupons->where('name', 'like', '%' . $request->keyword . '%');
            $discountCoupons->where('code', 'like', '%' . $request->keyword . '%');
        }

        $discountCoupons = $discountCoupons->paginate(10);
        return view('admin.coupon.list', compact('discountCoupons'));

    }
    public function create(){
        return view('admin.coupon.create');
        
    }
    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'code'=> 'required',
            'type'=> 'required',
            'discount_amount'=>'required|numeric',
            'status'=>'required',

        ]);

        if($validator->passes()){
            if(!empty($request->starts_at)){
                $now=Carbon::now();
                $startAt=Carbon::createFromFormat('Y-m-d',$request->starts_at);

                if($startAt->lte($now)==true){
                    return response()->json([
                        'status'=>false,
                        'errors' =>['starts_at'=>'Start date can not be less then current time']
                    ]);

                }
            }
            if(!empty($request->starts_at)&&!empty($request->expires_at) ){
                
                $expiresAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startsAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                

                if($expiresAt->gt($startsAt)==false){
                    return response()->json([
                        'status'=>false,
                        'errors' =>['expires_at'=>'Expire date must greater then Start date']
                    ]);

                }
            }
            $discountCode=new DiscountCoupon();
            $discountCode->code=$request->code;
            $discountCode->description=$request->description;
            $discountCode->max_uses=$request->max_uses;
            $discountCode->max_uses_user=$request->max_uses_user;
            $discountCode->type=$request->type;
            $discountCode->discount_amount=$request->discount_amount;
            $discountCode->min_amount=$request->min_amount;
            $discountCode->status=$request->status;
            $discountCode->starts_at=$request->starts_at;
            $discountCode->expires_at=$request->expires_at;
            $discountCode->save();

            session()->flash('success','Discount coupon added successfully');
            return response()->json([
                'status'=>true,
                'message' =>'Discount coupon added successfully'
            ]);

        }
        else{
            return response()->json([
                'status'=>false,
                'errors' =>$validator->errors()
            ]);
        }

    }
    public function edit(Request $request,$id){
        $coupon=DiscountCoupon::find($id);

        if($coupon==null){
            session()->flash('error','Record Not Found');
            return redirect()->route('coupons.index');
        }
        $data['coupon']=$coupon;
        return view('admin.coupon.edit',$data);


    }
    public function update(Request $request,$id){
        $discountCode=DiscountCoupon::find($id);

        if($discountCode == null){
            session()->flash('error','Record Not Found');
            return response()->json([
                'status'=>false
            ]);
           

        }
        $validator=Validator::make($request->all(),[
            'code'=> 'required',
            'type'=> 'required',
            'discount_amount'=>'required|numeric',
            'status'=>'required',

        ]);

        if($validator->passes()){
           
            if(!empty($request->starts_at)&&!empty($request->expires_at) ){
                
                $expiresAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);
                $startsAt=Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);
                

                if($expiresAt->gt($startsAt)==false){
                    return response()->json([
                        'status'=>false,
                        'errors' =>['expires_at'=>'Expire date must greater then Start date']
                    ]);

                }
            }
            
            $discountCode->code=$request->code;
            $discountCode->description=$request->description;
            $discountCode->max_uses=$request->max_uses;
            $discountCode->max_uses_user=$request->max_uses_user;
            $discountCode->type=$request->type;
            $discountCode->discount_amount=$request->discount_amount;
            $discountCode->min_amount=$request->min_amount;
            $discountCode->status=$request->status;
            $discountCode->starts_at=$request->starts_at;
            $discountCode->expires_at=$request->expires_at;
            $discountCode->save();

            session()->flash('success','Discount coupon updated successfully');
            return response()->json([
                'status'=>true,
                'message' =>'Discount coupon updated successfully'
            ]);

        }
        else{
            return response()->json([
                'status'=>false,
                'errors' =>$validator->errors()
            ]);
        }

    }
    
    public function destroy(Request $request,$id){
        $discountCode=DiscountCoupon::find($id);

        if($discountCode == null){
            session()->flash('error','Record Not Found');
            return response()->json([
                'status'=>false
            ]);
           
        }
        $discountCode->delete();
        session()->flash('success','Discount coupon deleted successfully');
        return response()->json([
            'status'=>true,
            'message' =>'Discount coupon deleted successfully'
        ]);


    }
    
}
