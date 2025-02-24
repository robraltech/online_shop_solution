<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    public function index(Request $request){

        $subCategories = SubCategory::latest();

        if ($request->filled('keyword')) {
            $subCategories->where('name', 'like', '%' . $request->keyword . '%');
        }

        $subCategories = $subCategories->paginate(10);
        return view('admin.sub_category.list', compact('subCategories'));
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
            'category_id' => 'required|exists:categories,id', // Ensure category exists
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
        ]);
    
        $request->session()->flash('success', 'Subcategory created successfully!');
    
        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Subcategory created successfully!',
            'data' => $subcategory
        ], 201);
    }
}
