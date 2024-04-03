<?php

use App\Http\Controllers\ChangePasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetRequestController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\BackOffice\ProductController;
use App\Http\Controllers\FrontOffice\Instagrammer\ProductInstagrammerController;

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
  Route::get('/filter', [UserController::class, 'filterUser']);
});


//product
Route::prefix('products')->group(function () {
  Route::get('/', [ProductController::class, 'index']);
  Route::post('/save', [ProductController::class, 'store']);
  Route::put('/update/{id}', [ProductController::class, 'update']);
  Route::delete('/delete/{id}', [ProductController::class, 'destroy']);
});

//instagrammer

Route::prefix('instagrammers')->group(function(){
  Route::get('products', [ProductInstagrammerController::class, 'index']);
  Route::post('/saveProduct', [ProductInstagrammerController::class, 'store']);
  Route::put('/updateProduct/{id}', [ProductInstagrammerController::class, 'update']);
  Route::delete('/deleteProduct/{id}', [ProductInstagrammerController::class, 'destroy']);
  Route::post('/addEchantillon', [ProductInstagrammerController::class, 'addEchantillon']);

});

//providers

Route::prefix('providers')->group(function(){
  Route::get('products', [ProductInstagrammerController::class, 'index']);
  Route::post('/saveProduct', [ProductInstagrammerController::class, 'store']);
  Route::put('/updateProduct/{id}', [ProductInstagrammerController::class, 'update']);
  Route::delete('/deleteProduct/{id}', [ProductInstagrammerController::class, 'destroy']);
});