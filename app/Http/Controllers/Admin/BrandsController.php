<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandsController extends Controller
{

    public function index(Request $request){
        $brands=Brands::latest('id');
        if($request->get('keyword')){
            $brands=$brands->where('name','like','%'.$request->get('keyword').'%');
        }
        $brands=$brands->paginate(10);
        return view('admin.brands.list',compact('brands'));

    }
    public function create()
    {
        return view('admin.brands.create');
    }
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:brands,slug',
        ]);

        if($validator->passes()){
            $brand=new Brands();
            $brand->name=$request->name;
            $brand->slug=$request->slug;
            $brand->status=$request->status;
            $brand->save();
            session()->flash('success', 'Brand added successfully!');

            return response()->json([
                'status'=>true,
                'message'=>'Brand added successfully',
                'redirect' => route('brands.index')
            ],201);

        }else{
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ]);
        }
        
    }

    public function edit(Request $request,$id)
    {
        $brand=Brands::find($id);
        if(!$brand){
            session()->flash('error','Brand not found');
            return redirect()->route('brands.index');
        }
        $data['brand']=$brand;
        return view('admin.brands.edit',$data);
       
    }

    public function update(Request $request,$id){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:brands,slug,'.$id,
        ]);

        if($validator->passes()){
            $brand=Brands::find($id);
            $brand->name=$request->name;
            $brand->slug=$request->slug;
            $brand->status=$request->status;
            $brand->save();
            session()->flash('success', 'Brand updated successfully!');

            return response()->json([
                'status'=>true,
                'message'=>'Brand updated successfully',
                'redirect' => route('brands.index')
            ],201);

        }else{
            return response()->json([
                'status'=>false,
                'message'=>$validator->errors()
            ]);
        }
    }
    public function destroy(Request $request, $id)
    {
        $brand = Brands::find($id);
    
        if (!$brand) {
            return response()->json([
                'status' => false,
                'message' => 'Brand not found'
            ], 404);
        }
    
        $brand->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully',
            'redirect' => route('brands.index')
        ]);
    }
    
}
