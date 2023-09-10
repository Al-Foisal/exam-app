<?php

use App\Http\Controllers\Backend\AdminAuthenticationController;
use App\Http\Controllers\Backend\DashboardController;
use Illuminate\Support\Facades\Route;

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

Route::controller(AdminAuthenticationController::class)->middleware('guest:admin')->group(function () {

    Route::get('/', 'login')->name('login');
    Route::post('/store-login', 'storeLogin')->name('storeLogin');
});

Route::middleware('auth:admin')->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::post('/logout', 'logout')->name('logout');
    });
});
