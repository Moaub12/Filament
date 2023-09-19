<?php


use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
   

    Route::get('clients/{clientId}', [ClientController::class, 'getClientDetails']);

    Route::put('clients/{clientId}', [ClientController::class, 'updateClientDetails']);
    Route::post('clients/{clientId}/addresses', [ClientController::class, 'createAddress']);

    Route::group(['prefix'=>'order'],function(){
        Route::post('/draft',[OrderController::class,'create']);
        Route::post('/placeOrder',[OrderController::class,'placeOrder']);
        Route::get('/products',[OrderController::class,'products']);
        Route::delete('/remove-product/{product}',[OrderController::class,'removeProduct']);
    });
   
    Route::post('/coupon',[CouponController::class,'validateCoupon']);
   
    Route::get('/addons-removes/{id}',[OrderController::class,'getAddRemoveProduct']);


    

});



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgotpassword', [NewPasswordController::class, 'forgotPassword']);
Route::get('/products',[ProductController::class,'index']);
Route::resource('/products',ProductController::class);
Route::get('/products/{id}',[ProductController::class,'show']);
Route::get('/categories',[CategoryController::class,'index']);
Route::get('/categories/{id}/products',[CategoryController::class,'productsByCategory']);
Route::get('/tags',[TagController::class,'index']);
Route::get('/tags/{id}/products',[TagController::class,'productsByTag']);
Route::get('/top-products',[ProductController::class,'getProductsOrderedByCounter']);
Route::post('/products/filter', [ProductController::class, 'filterByPrice']);




