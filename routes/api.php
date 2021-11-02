<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post("send/invitation", [AdminController::class, 'sendInvitation']); 
    Route::post("user/signup", [UserController::class, 'signUp']); 
    Route::get("profile", [UserController::class, 'profile']);    
 
    Route::post("update/profile", [UserController::class, 'updateProfile']); 

});

Route::post("login", [LoginController::class, 'index']); 
Route::post("verify/pin", [UserController::class, 'verify']); 
Route::post("userlogin", [UserController::class, 'userLogin']); 
Route::get("test", [LoginController::class, 'test']); 