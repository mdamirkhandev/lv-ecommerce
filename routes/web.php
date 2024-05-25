<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\user\ShopController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCatController;
use App\Http\Controllers\user\HomeController as UserHomeController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [UserHomeController::class, 'index'])->name('user.index');
Route::get('/shop/{catSlug?}/{subCatSlug?}', [ShopController::class, 'index'])->name('user.shop');
Route::get('/product/{slug}', [ShopController::class, 'product'])->name('user.product');
Route::resource('/cart', CartController::class);
Route::group(['prefix' => 'admin'], function () {
    //guest
    Route::group(['middleware' => 'admin.guest'], function () {
        Route::post('/login-process', [AdminLoginController::class, 'loginProcess'])->name('admin.login-process');
        Route::get('/login', [AdminLoginController::class, 'index'])->name('admin.login');
    });
    //auth
    Route::group(['middleware' => 'admin.auth'], function () {
        Route::get('/home', [HomeController::class, 'home'])->name('admin.home');
        Route::get('/logout', [HomeController::class, 'logout'])->name('admin.logout');
        Route::post('/upload-temp-image', [TempImagesController::class, 'create'])->name('temp-images.create');
        //Categories Routes
        Route::resource('/categories', CategoryController::class);
        //Sub-Categories Routes
        Route::resource('/sub-category', SubCategoryController::class);
        //Brands Routes
        Route::resource('/brands', BrandController::class);
        //Products Routes
        Route::resource('/products', ProductController::class);
        //Products sub cate Routes
        Route::get('/product-sub-cat', [ProductSubCatController::class, 'index'])->name('admin.productSubCat');
        //Product Images
        Route::post('/product-images/update', [ProductImageController::class, 'update'])->name('admin.product-images.update');
        Route::delete('/product-images', [ProductImageController::class, 'destroy'])->name('admin.product-images.delete');
        //create category slug
        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if (!empty($request->title)) {
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status' => true,
                'slug' => $slug
            ]);
        })->name('getslug');
    });
});
