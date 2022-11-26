<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware'=>'api'],function($routes){
    Route::controller(UserController::class)->group(function(){
        Route::post('register-user','create');
        Route::post('login-user','login');
        Route::post('user-profile','userProfile');
        Route::post('refresh-token','refreshToken');
        Route::post('logout','logout');
    });

});
