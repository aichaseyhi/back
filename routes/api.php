<?php

use App\Http\Controllers\ChangePasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetRequestController;
use App\Http\Controllers\FrontOffice\Client\ClientController;
use App\Http\Controllers\BackOffice\MessageController;
use App\Http\Controllers\BackOffice\OrderController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\BackOffice\ProductController;
use App\Http\Controllers\BackOffice\SubcategoryController;
use App\Http\Controllers\FrontOffice\Instagrammer\ProductInstagrammerController;
use App\Http\Controllers\FrontOffice\Instagrammer\InstagrammerController;
use App\Http\Controllers\FrontOffice\Provider\ProductProviderController;
use App\Http\Controllers\FrontOffice\Provider\ProviderController;



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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  //  return $request->user();
//});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('user', 'user');
    Route::post('forgetPassword', 'forgetPassWord');
    Route::post('verifCode', 'verifCode');
    Route::put('changePassword', 'changePassword');
    Route::put('updateUserPassword/{id}', 'updateUserPassword');
   
});


//user
Route::prefix('users')->group(function () {
  Route::post('/reset-password-request', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
  Route::post('/change-password', [ChangePasswordController::class, 'passwordResetProcess']);
  Route::get('/user/{Role}',[UserController::class, 'getUsersByRole']);
  Route::get('/',[UserController::class, 'index']);
  Route::post('/save',[UserController::class, 'store']);
  Route::get('/show/{id}',[UserController::class, 'show']);
  Route::delete('/destroy/{id}',[UserController::class, 'destroy']);
  Route::put('/update/{id}',[UserController::class, 'update']);
  Route::get('/filter', [UserController::class, 'filterUser']);
  Route::put('/updateUserStatus/{id}', [UserController::class, 'updateUserStatus']);
});


//product
Route::prefix('products')->group(function () {
  Route::get('/', [ProductController::class, 'index']);
  Route::post('/save', [ProductController::class, 'store']);
  Route::put('/update/{id}', [ProductController::class, 'update']);
  Route::delete('/delete/{id}', [ProductController::class, 'destroy']);
  Route::get('filterProduct', [ProductController::class, 'filterProduct']);
  Route::get('/show/{id}',[ProductController::class, 'show']);
  Route::put('/updateProductStatus/{id}', [ProductController::class, 'updateProductStatus']);
  Route::get('/filterProducts',[ProductController::class, 'filterProducts']);

});

//subCategory
Route::prefix('subCategories')->group(function () {
  Route::get('/', [SubcategoryController::class, 'index']);
  Route::post('/save', [SubcategoryController::class, 'store']);
  Route::put('/update/{id}', [SubcategoryController::class, 'update']);
  Route::delete('/delete/{id}', [SubcategoryController::class, 'destroy']);
  Route::get('/show/{id}',[SubcategoryController::class, 'show']);
});

//messages
Route::prefix('message')->group(function () {
  Route::get('/', [MessageController::class, 'index']);  
  Route::put('/update/{id}', [MessageController::class, 'update']);
  Route::delete('/delete/{id}', [MessageController::class, 'destroy']);
  Route::get('/show/{id}',[MessageController::class, 'show']);
});

//orders
Route::prefix('orders')->group(function () {
  Route::get('/', [OrderController::class, 'index']);
  Route::post('/save', [OrderController::class, 'store']);
  Route::put('/update/{id}', [OrderController::class, 'update']);
  Route::delete('/delete/{id}', [OrderController::class, 'destroy']);
  Route::get('/show/{id}',[OrderController::class, 'show']);
  Route::put('/updateOrderStatus/{id}', [OrderController::class, 'updateOrderStatus']);
  Route::get('/filterOrders', [OrderController::class, 'filterOrders']);
});

//instagrammer

Route::prefix('instagrammers')->group(function(){
  Route::get('products', [ProductInstagrammerController::class, 'index']);
  Route::post('/saveProduct', [ProductInstagrammerController::class, 'store']);
  Route::put('/updateProduct/{id}', [ProductInstagrammerController::class, 'update']);
  Route::delete('/deleteProduct/{id}', [ProductInstagrammerController::class, 'destroy']);
  Route::get('/showProduct/{id}',[ProductInstagrammerController::class, 'show']);
  Route::post('/addEchantillon', [InstagrammerController::class, 'addEchantillon']);
  Route::post('/addProductProvider', [InstagrammerController::class, 'addProductProvider']);
  Route::get('/getInstagrammerProducts', [InstagrammerController::class, 'getInstagrammerProducts']);
  Route::post('/sendMessage', [InstagrammerController::class, 'sendMessage']);
  Route::put('updateSelfData',[InstagrammerController::class, 'updateSelfData']);
  Route::get('/getProviderProducts', [InstagrammerController::class, 'getProviderProducts']);



});

//providers

Route::prefix('providers')->group(function(){
  Route::get('products', [ProductProviderController::class, 'index']);
  Route::post('/saveProduct', [ProductProviderController::class, 'store']);
  Route::put('/updateProduct/{id}', [ProductProviderController::class, 'update']);
  Route::put('/updateEchantillon/{id}', [ProviderController::class, 'updateEchantillon']);
  Route::get('/getProviderProducts', [ProviderController::class, 'getProviderProducts']);
  Route::put('updateSelfData',[ProviderController::class, 'updateSelfData']);
  Route::post('/sendMessage', [ProviderController::class, 'sendMessage']);

});

//client
Route::prefix('clients')->group(function(){
  Route::get('/getProductById/{id}', [ClientController::class, 'getProductById']);
  Route::post('/addOrder', [ClientController::class, 'addOrder']);
  Route::put('/updateOrder/{id}', [ClientController::class, 'updateOrder']);
  Route::put('/cancelOrder/{id}', [ClientController::class, 'cancelOrder']);
  Route::put('/confirmOrder/{id}', [ClientController::class, 'confirmOrder']);

});
