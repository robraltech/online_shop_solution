<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandsController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DiscountCodeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;
use App\Http\Controllers\admin\ShippingController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/',[FrontController::class,'index'])->name('front.home');
Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');
Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');
Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-To-Cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-Cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem.cart');
Route::get('/checkout',[CartController::class,'chekout'])->name('front.chekout');
Route::post('/process-checkout',[CartController::class,'processCheckout'])->name('front.processCheckout');
Route::post('/thanks/{orderId}',[CartController::class,'thankyou'])->name('front.thankyou');
Route::post('/get-order-summery',[CartController::class,'getOrderSummery'])->name('front.getOrderSummery');
Route::post('/apply-discount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');
Route::post('/get-order-summery',[CartController::class,'getOrderSummery'])->name('front.getOrderSummery');
Route::post('/apply-discount',[CartController::class,'applyDiscount'])->name('front.applyDiscount');
Route::post('/remove-discount',[CartController::class,'removeCoupon'])->name('front.removeCoupon');

Route::group(['prefix'=>'account'],function(){
    Route::group(['middleware'=>'guest'],function(){
        Route::get('/login',[AuthController::class,'login'])->name('account.login');
        Route::post('/login',[AuthController::class,'authenticate'])->name('account.authenticate');
        Route::get('/register',[AuthController::class,'register'])->name('account.register');
        Route::post('/process-register',[AuthController::class,'processRegister'])->name('account.processRegister');
    });
    Route::group(['middleware'=>'auth'],function(){
        Route::get('/profile',[AuthController::class,'profile'])->name('account.profile');
        Route::get('/my-orders',[AuthController::class,'orders'])->name('account.orders');
        Route::get('/order-detail/{orderId}',[AuthController::class,'orderDetail'])->name('account.orderDetail');
        Route::get('/logout',[AuthController::class,'logout'])->name('account.logout');

    });
});
Route::group(['prefix'=>'admin'],function(){
    Route::group(['middleware'=>'admin.guest'],function(){
        Route::get('/login',[AdminController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[AdminController::class,'authenticate'])->name('admin.authenticate');
    });

    Route::group(['middleware'=>'admin.auth'],function(){
        Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');

        //categories
        Route::get('/category',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/category/create',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories/store',[CategoryController::class,'store'])->name('categories.store');
        Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');


        //sub categories
        Route::get('/sub-category/create',[SubCategoryController::class,'create'])->name('sub-categories.create');
        Route::get('/sub-category',[SubCategoryController::class,'index'])->name('sub-categories.index');
        Route::post('/sub-category/store',[SubCategoryController::class,'store'])->name('sub-categories.store');
        Route::get('/sub-category/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('sub-categories.edit');
        Route::put('/sub-category/{subCategory}',[SubCategoryController::class,'update'])->name('sub-categories.update');
        Route::delete('/sub-category/{subCategory}',[SubCategoryController::class,'destroy'])->name('sub-categories.delete');

        //brand
        Route::get('/brands/create',[BrandsController::class,'create'])->name('brands.create');
        Route::post('/brands/store',[BrandsController::class,'store'])->name('brands.store');
        Route::get('/brands',[BrandsController::class,'index'])->name('brands.index');
        Route::get('/brands/{brand}/edit',[BrandsController::class,'edit'])->name('brand.edit');
        Route::put('/brands/{brand}',[BrandsController::class,'update'])->name('brand.update');
        Route::delete('/brands/{brand}',[BrandsController::class,'destroy'])->name('brands.delete');

        //Products
        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
        Route::post('/products/store',[ProductController::class,'store'])->name('products.store');
        Route::get('/products/{product}/edit',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{product}',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}',[ProductController::class,'destroy'])->name('products.delete');

        Route::get('/get-products',[ProductController::class,'getProducts'])->name('products.getProducts');

        Route::get('/product-subcategories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');

        //shipping
        Route::get('/shipping/create',[ShippingController::class,'create'])->name('shipping.create');
        Route::post('/shipping/store',[ShippingController::class,'store'])->name('shipping.store');
        Route::get('/shipping/{shipping}/edit',[ShippingController::class,'edit'])->name('shipping.edit');
        Route::put('/shipping/{shipping}',[ShippingController::class,'update'])->name('shipping.update');
        Route::delete('/shipping/{shipping}',[ShippingController::class,'destroy'])->name('shipping.delete');

        //coupons code
        Route::get('/coupons',[DiscountCodeController::class,'index'])->name('coupons.index');
        Route::get('/coupons/create',[DiscountCodeController::class,'create'])->name('coupons.create');
        Route::post('/coupons/store',[DiscountCodeController::class,'store'])->name('coupons.store');
        Route::get('/coupons/{coupon}/edit',[DiscountCodeController::class,'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}',[DiscountCodeController::class,'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}',[DiscountCodeController::class,'destroy'])->name('coupons.delete');

        //orders
        Route::get('/orders',[OrderController::class,'index'])->name('orders.index');
        Route::get('/orders/{id}',[OrderController::class,'detail'])->name('orders.detail');
        Route::post('/orders/change-Status/{id}',[OrderController::class,'changeOrderStatus'])->name('orders.changeOrderStatus');
        
        Route::post('/upload-image',[TempImageController::class,'create'])->name('temp-images.create');

        Route::post('/product-image/update',[ProductImageController::class,'update'])->name('product-image.update');
        Route::delete('/product-image',[ProductImageController::class,'destroy'])->name('product-image.destroy');
        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if ($request->name) {
                $slug = Str::slug($request->name);
            }

            return response()->json([
                'status' => true,
                'slug' => strtolower($slug)
            ]);
        })->name('getSlug');
    });


});
