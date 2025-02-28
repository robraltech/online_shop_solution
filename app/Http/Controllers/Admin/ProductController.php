<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brands;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;



class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::latest('id')->with('product_images');
        // dd($products);
        if ($request->get('keyword') != '') {
            $products->where('title', 'like', '%' . $request->keyword . '%');
        }

        $products = $products->paginate(10);
        $data['products'] = $products;
        return view('admin.products.list', $data);
    }
    public function create()
    {
        $data = [];
        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brands::orderBy('name', 'asc')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        return view('admin.products.create', $data);
    }
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' =>  'required',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required',
            'category' => 'required',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brands;
            $product->is_featured = $request->is_featured;
            $product->status = $request->status;
            $product->save();

            if (!empty($request->images_array)) {
                foreach ($request->images_array as $image) {
                    $tempImageInfo = TempImage::find($image);
                    $extArray = explode(',', $tempImageInfo->name);
                    $ext = last($extArray);

                    $productImage = new ProductImage();
                    $productImage->products_id = $product->id;
                    $productImage->image = '';
                    $productImage->save();

                    $imageName = $product->id . '-' . $productImage->id . '-' . time() . '-' . $ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    $sourcePath = public_path('temp/' . $tempImageInfo->name);
                    $largeDestinationPath = public_path('uploads/products/large/' . $imageName);
                    $smallDestinationPath = public_path('uploads/products/small/' . $imageName);

                    // Move the original image to the large folder
                    if (file_exists($sourcePath)) {
                        copy($sourcePath, $largeDestinationPath);

                        // Create a resized small version manually
                        list($width, $height) = getimagesize($sourcePath);
                        $smallImage = imagecreatefromstring(file_get_contents($sourcePath));

                        $newWidth = 300;
                        $newHeight = 300;
                        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($resizedImage, $smallImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                        // Save the resized image
                        if ($ext == 'jpg' || $ext == 'jpeg') {
                            imagejpeg($resizedImage, $smallDestinationPath, 90);
                        } elseif ($ext == 'png') {
                            imagepng($resizedImage, $smallDestinationPath);
                        } elseif ($ext == 'gif') {
                            imagegif($resizedImage, $smallDestinationPath);
                        }

                        // Free memory
                        imagedestroy($smallImage);
                        imagedestroy($resizedImage);
                    }
                }
            }

            session()->flash('success', 'Product added successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Product added successfully',
                'redirect' => route('products.index')
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $product = Product::find($id);
        $subCategories = SubCategory::where('category_id', $product->category_id)->get();
        $productImage = ProductImage::where('products_id', $product->id)->get();
        $data = [];

        $categories = Category::orderBy('name', 'asc')->get();
        $brands = Brands::orderBy('name', 'asc')->get();
        $data['categories'] = $categories;
        $data['brands'] = $brands;
        $data['product'] = $product;
        $data['subCategories'] = $subCategories;
        $data['productImage'] = $productImage;
        return view('admin.products.edit', $data);
    }

    public function update($id, Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' =>  'required|unique:products,slug,' . $id . ',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,' . $id . ',id',
            'track_qty' => 'required',
            'category' => 'required',
            'is_featured' => 'required|in:0,1',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $product = Product::findOrFail($id); // Ensure product exists

            $product->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'description' => $request->description,
                'price' => $request->price,
                'compare_price' => $request->compare_price,
                'sku' => $request->sku,
                'barcode' => $request->barcode,
                'track_qty' => $request->track_qty,
                'qty' => $request->qty,
                'category_id' => $request->category,
                'sub_category_id' => $request->sub_category ?? null, // Handle optional fields
                'brand_id' => $request->brands ?? null,
                'is_featured' => $request->is_featured,
                'status' => $request->status,
            ]);

            

            session()->flash('success', 'Product updated successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'redirect' => route('products.index')
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }
    }

    public function destroy(Request $request ,$id){
        $product=Product::find($id);

        if(empty($product)){
            return response()->json([
                'status' =>false,
                'notfound'=>true

            ]);
        }
        $productImages=ProductImage::where('products_id',$id)->get();
        if(!empty($productImages)){
            foreach($productImages as $productImage ){
                File::delete(public_path('uploads/products/large/' . $productImage->image));
                File::delete(public_path('uploads/products/small/' . $productImage->image));

            }
        }
        $product->delete();
        session()->flash('success','Product deleted Successfully');
        if(empty($product)){
            return response()->json([
                'status'=>true,
                'message'=> 'Product deleted Successfully'
                
            ]);
        }
    }
}
