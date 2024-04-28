<?php

use App\Http\Controllers\ChangePasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetRequestController;
use App\Http\Controllers\BackOffice\MessageController;
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
    Route::put('update/{id}', 'update');

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
  Route::delete('/update/{id}',[UserController::class, 'update']);
  Route::get('/filter', [UserController::class, 'filterUser']);
  Route::put('/updateUserStatus/{id}', [UserController::class, 'updateUserStatus']);
  Route::get('/messages', [MessageController::class, 'index']);
});


//product
Route::prefix('products')->group(function () {
  Route::get('/', [ProductController::class, 'index']);
  Route::post('/save', [ProductController::class, 'store']);
  Route::put('/update/{id}', [ProductController::class, 'update']);
  Route::delete('/delete/{id}', [ProductController::class, 'destroy']);
  Route::get('filterProduct', [ProductController::class, 'filterProduct']);

});

//subCategory
Route::prefix('subCategories')->group(function () {
  Route::get('/', [SubcategoryController::class, 'index']);
  Route::post('/save', [SubcategoryController::class, 'store']);
  Route::put('/update/{id}', [SubcategoryController::class, 'update']);
  Route::delete('/delete/{id}', [SubcategoryController::class, 'destroy']);
});

//instagrammer

Route::prefix('instagrammers')->group(function(){
  Route::get('products', [ProductInstagrammerController::class, 'index']);
  Route::post('/saveProduct', [ProductInstagrammerController::class, 'store']);
  Route::put('/updateProduct/{id}', [ProductInstagrammerController::class, 'update']);
  Route::delete('/deleteProduct/{id}', [ProductInstagrammerController::class, 'destroy']);
  Route::post('/addEchantillon', [InstagrammerController::class, 'addEchantillon']);
  Route::post('/addProductProvider', [InstagrammerController::class, 'addProductProvider']);
  Route::get('/getInstagrammerProducts', [InstagrammerController::class, 'getInstagrammerProducts']);
  Route::post('/sendMessage', [InstagrammerController::class, 'sendMessage']);


});

//providers

Route::prefix('providers')->group(function(){
  Route::get('products', [ProductProviderController::class, 'index']);
  Route::post('/saveProduct', [ProductProviderController::class, 'store']);
  Route::put('/updateProduct/{id}', [ProductProviderController::class, 'update']);
  Route::put('/updateEchantillon/{id}', [ProviderController::class, 'updateEchantillon']);
  Route::get('/getProviderProducts', [ProviderController::class, 'getProviderProducts']);

});