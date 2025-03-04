<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create(){
        $countries=Country::get();
        $data['countries']=$countries;
        $shippingCharges=ShippingCharge::select('shipping_charges.*','countries.name')->leftJoin('countries','countries.id','shipping_charges.country_id')->get();
        $data['shippingCharges']=$shippingCharges;
        return view('admin.shipping.create',$data);
    }

    public function store(Request $request){
        $validator=Validator::make($request->all(),[
            'country' =>'required',
            'amount'=> 'required|numeric',
        ]);

        if($validator->passes()){
            $count=ShippingCharge::where('country_id',$request->country)->count();
            if($count >0){
                session()->flash('success','shipping already added');
            return response()->json([
                'status' =>true
                
            ]);
            }
            $shipping=new ShippingCharge();
            $shipping->country_id=$request->country_id;
            $shipping->amount=$request->amount;
            $shipping->save();

            session()->flash('success','shipping added successfully');
            return response()->json([
                'status' =>true,
                'message' =>'shipping added successfully'

            ]);
        }
        else{
            return response()->json([
                'status' =>false,
                'errors' =>$validator->errors()

            ]);
        }

    }

    public function edit($id){
        $shippingCharge=ShippingCharge::find($id);
        $countries=Country::get();
        $data['countries']=$countries;
        $data['shippingCharge']=$shippingCharge;
        return view('admin.shipping.edit',$data);
    }

    public function update($id,Request $request){
        $validator=Validator::make($request->all(),[
            'country' =>'required',
            'amount'=> 'required|numeric',
        ]);

        if($validator->passes()){
            $shipping=ShippingCharge::find($id);
            $shipping->country_id=$request->country_id;
            $shipping->amount=$request->amount;
            $shipping->save();

            session()->flash('success','shipping updated successfully');
            return response()->json([
                'status' =>true,
                'message' =>'shipping updated successfully'

            ]);
        }
        else{
            return response()->json([
                'status' =>false,
                'errors' =>$validator->errors()

            ]);
        }

    }

    public function destroy($id){
        $shippingCharge=ShippingCharge::find($id);

        if($shippingCharge==null){
            session()->flash('error','shipping not found');
            return response()->json([
                'status' =>true,
                
            ]);
        }


        $shippingCharge->delete();
        session()->flash('success','shipping deleted successfully');
            return response()->json([
                'status' =>true,
                
            ]);
    }
}
