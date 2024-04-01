<?php

use App\Http\Controllers\ChangePasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetRequestController;
use App\Http\Controllers\BackOffice\UserController;
use App\Http\Controllers\BackOffice\ProduitController;



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
Route::post('/reset-password-request', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
Route::post('/change-password', [ChangePasswordController::class, 'passwordResetProcess']);
Route::get('/user/{Role}',[UserController::class, 'getUsersByRole']);

Route::get('/users',[UserController::class, 'index']);

Route::post('/save',[UserController::class, 'store']);
Route::get('/show/{id}',[UserController::class, 'show']);
Route::delete('/destroy/{id}',[UserController::class, 'destroy']);






//produit
Route::get('/produits',[ProduitController::class, 'index']);

Route::post('/saveProduit',[ProduitController::class, 'store']);

Route::put('/updateProduit/{id}',[ProduitController::class, 'update']);

Route::delete('/deleteProduit/{id}',[ProduitController::class, 'destroy']);