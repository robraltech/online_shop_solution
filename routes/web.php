<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\TempImageController;
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

Route::get('/', function () {
    return view('welcome');
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
        Route::get('/category',[CategoryController::class,'index'])->name('category.index');
        Route::get('/category/create',[CategoryController::class,'create'])->name('category.create');
        Route::post('/categories/store',[CategoryController::class,'store'])->name('category.store');
        Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('category.delete');
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('category.update');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('category.edit');
         
        //sub categories
        Route::get('/sub-category/create',[SubCategoryController::class,'create'])->name('sub-category.create');
        Route::get('/sub-category',[SubCategoryController::class,'index'])->name('sub-category.index');
        Route::post('/sub-category/store',[SubCategoryController::class,'store'])->name('sub-category.store');

        Route::post('/upload-image',[TempImageController::class,'create'])->name('temp-images.create');
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
