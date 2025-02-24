<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\TempImage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::latest();

        if ($request->filled('keyword')) {
            $categories->where('name', 'like', '%' . $request->keyword . '%');
        }

        $categories = $categories->paginate(10);
        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'image_id' => 'nullable|exists:temp_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'status' => $request->status ?? 0,
        ]);

        // Handle Image Upload
        if ($request->filled('image_id')) {
            $this->moveImage($category, $request->image_id);
        }

        $request->session()->flash('message', 'Category added successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category added successfully',
            'category' => $category,
        ], 201);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('category.index');
        }
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
            'image_id' => 'nullable|exists:temp_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = Category::findOrFail($id);
        $oldImage = $category->image;

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'status' => $request->status ?? 0,
        ]);

        // Handle Image Update
        if ($request->filled('image_id')) {
            $this->deleteImage($oldImage);
            $this->moveImage($category, $request->image_id);
        }

        $request->session()->flash('message', 'Category updated successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'category' => $category,
        ], 200);
    }

    public function destroy(Request $request ,$id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('category.index');
        }

        $this->deleteImage($category->image);
        $category->delete();
        $request->session()->flash('message', 'Category deleted successfully');
        
        return redirect()->route('category.index');
    }

    // Helper Function to Move Image from Temp Folder
    private function moveImage(Category $category, $imageId)
    {
        $tempImage = TempImage::find($imageId);

        if ($tempImage) {
            $ext = pathinfo($tempImage->name, PATHINFO_EXTENSION);
            $newImageName = $category->id . '-' . time() . '.' . $ext;

            $sourcePath = public_path('/temp/' . $tempImage->name);
            $destinationPath = public_path('/uploads/category/');
            $thumbPath = public_path('/uploads/category/thumb/');

            // Ensure directories exist
            File::ensureDirectoryExists($destinationPath, 0777, true);
            File::ensureDirectoryExists($thumbPath, 0777, true);

            // Move file if it exists
            if (File::exists($sourcePath)) {
                File::move($sourcePath, $destinationPath . $newImageName);
                $category->update(['image' => $newImageName]);
            }
        }
    }

    // Helper Function to Delete Old Image
    private function deleteImage($imageName)
    {
        if ($imageName) {
            $imagePath = public_path('/uploads/category/' . $imageName);
            $thumbPath = public_path('/uploads/category/thumb/' . $imageName);

            File::delete([$imagePath, $thumbPath]);
        }
    }
}
