<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

$currentVersion = 'v1';

//----------------------------------------------------AUTHENTICATION ROUTES------------------------------------------
Route::group([
    'prefix' => "$currentVersion/auth"
], function () {
    //public routes
    Route::post('system/login', [AuthController::class, 'systemLogin'])->name('System Login');

    //private routes
    Route::group(['middleware' => 'auth:api'], function () {
        //general routes
        Route::get('logout', [AuthController::class, 'logout'])->name('Logout');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('Change Password');
        Route::post('add-expired-time', [AuthController::class, 'addExpiredTime'])->name('Add Expired Time');

    });
});

Route::group([
    'prefix' => "$currentVersion"
], function () {
    //public routes

    //private routes
    Route::group(['middleware' => 'auth:api'], function () {
        //general routes
        Route::get('get-user-info', [UserController::class, 'getUserInfo'])->name('Get User Info');
    });
});
