<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    public function index(Request $request)
    {

        $categories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')->latest('sub_categories.id')->leftjoin('categories', 'categories.id', '=', 'sub_categories.category_id');

        if (!empty($request->get('keyword'))) {
            $categories=$categories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
        }

        $categories = $categories->paginate(10);
        return view('admin.sub_category.list', compact('categories'));
    }
    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create', $data);
    }
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:sub_categories,slug',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|integer',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Store the subcategory in the database
        $subcategory = SubCategory::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'showHome' => $request->showHome,
        ]);
        // Flash success message
        session()->flash('success', 'Subcategory created successfully!');

        return response()->json([
            'status' => true,
            'message' => 'Subcategory created successfully!',
            'redirect' => route('sub-category.index')
        ], 201);
    }

    public function edit($id,Request $request){
        $subCategories=SubCategory::find($id);
        if(!$subCategories){
            session()->flash('error','Sub Category not found');
            return redirect()->route('sub-category.index');
        }
        $categories = Category::orderBy('name', 'asc')->get();
        $data['categories'] = $categories;
        $data['subCategories']=$subCategories;
        return view('admin.sub_category.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $subcategory = SubCategory::find($id);
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|unique:sub_categories,slug,'.$subcategory->id.'id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|integer',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // Store the subcategory in the database
        
        $subcategory->name = $request->name;
        $subcategory->slug = $request->slug;
        $subcategory->category_id = $request->category_id;
        $subcategory->status = $request->status;
        $subcategory->showHome = $request->showHome;
        $subcategory->save();
        // Flash success message
        session()->flash('success', 'Subcategory updated successfully!');

        return response()->json([
            'status' => true,
            'message' => 'Subcategory updated successfully!',
            'redirect' => route('sub-category.index')
        ], 201);
    }

    public function destroy(Request $request ,$id)
    {
        $subcategory = SubCategory::find($id);

        if (!$subcategory) {
            return redirect()->route('sub-category.index');
        }

        $subcategory->delete();
        
        session()->flash('message', 'Category deleted successfully');
        
        return redirect()->route('sub-category.index');
    }

}
