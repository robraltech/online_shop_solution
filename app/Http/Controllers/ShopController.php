<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null)
    {
        // Initialize variables properly
        $categorySelected = null;
        $subCategorySelected = null;
        $brandsArray = [];
        $sort = $request->get('sort', ''); // Initialize $sort with a default value

        // Fetch categories and brands
        $categories = Category::orderBy("name", "asc")->with('sub_category')->where('status', '1')->get();
        $brands = Brand::orderBy("name", "asc")->where('status', '1')->get();
        
        // Start building the product query
        $products = Product::where('status', '1');

        // Apply filters
        if (!empty($categorySlug)) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $products = $products->where('category_id', $category->id);
                $categorySelected = $category->id;
            }
        }

        if (!empty($subCategorySlug)) {
            $subCategory = SubCategory::where('slug', $subCategorySlug)->first();
            if ($subCategory) {
                $products = $products->where('sub_category_id', $subCategory->id);
                $subCategorySelected = $subCategory->id;
            }
        }

        if (!empty($request->get('brand'))) {
            $brandsArray = explode(',', $request->get('brand'));
            $products = $products->whereIn('brand_id', $brandsArray);
        }

        if ($request->has('price_min') && $request->has('price_max')) {
            $priceMin = intval($request->get('price_min', 0));
            $priceMax = intval($request->get('price_max', 1000));

            $products = $products->whereBetween('price', [$priceMin, $priceMax]);
        }

        // Sorting
        switch ($sort) {
            case 'latest':
                $products = $products->orderBy('id', 'desc');
                break;
            case 'price_asc':
                $products = $products->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $products = $products->orderBy('price', 'desc');
                break;
            default:
                $products = $products->orderBy('id', 'desc');
                break;
        }

        // Paginate results
        $products = $products->paginate(12);

        // Prepare data for the view
        $data = [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'categorySelected' => $categorySelected,
            'subCategorySelected' => $subCategorySelected,
            'brandsArray' => $brandsArray,
            'priceMax' => $request->get('price_max', 1000),
            'priceMin' => $request->get('price_min', 0),
            'sort' => $sort, // Pass the sort option to the view if needed
        ];

        return view('front.shop', $data);
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->with('product_images')->first();

        if ($product == null) {
            abort(404);
        }

        $relatedProduct = [];
        if (!empty($product->related_products)) {
            $productArray = explode(',', $product->related_products);
            $relatedProduct = Product::whereIn('id', $productArray)->get();
        }

        return view('front.product', [
            'product' => $product,
            'relatedProduct' => $relatedProduct
        ]);
    }
}
