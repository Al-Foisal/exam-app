<?php

use App\Http\Controllers\Backend\AdminAuthenticationController;
use App\Http\Controllers\Backend\AdminManagementController;
use App\Http\Controllers\Backend\CompanyInfoController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\SubjectController;
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
    Route::get('/forgot-password', 'forgotPassword')->name('forgotPassword');
    Route::post('/store-forgot-password', 'storeForgotPassword')->name('storeForgotPassword');
    Route::get('/reset-password/{token}', 'resetPassword')->name('resetPassword');
    Route::post('/store-reset-password', 'storeResetPassword')->name('storeResetPassword');
});

Route::middleware('auth:admin')->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::post('/logout', 'logout')->name('logout');
    });

    Route::controller(AdminManagementController::class)->prefix('/admin')->name('admin.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{admin}', 'edit')->name('edit');
        Route::put('/update/{admin}', 'update')->name('update');
        Route::delete('/delete/{admin}', 'delete')->name('delete');
    });

    Route::controller(SubjectController::class)->prefix('/subject')->name('subject.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{subject}', 'edit')->name('edit');
        Route::put('/update/{subject}', 'update')->name('update');
        Route::delete('/delete/{subject}', 'delete')->name('delete');
    });

    Route::get('/company-info', [CompanyInfoController::class, 'showCompanyInfo'])->name('showCompanyInfo');
    Route::post('/company-info', [CompanyInfoController::class, 'storeCompanyInfo'])->name('storeCompanyInfo');

    Route::controller(PageController::class)->prefix('/page')->name('page.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{page}', 'edit')->name('edit');
        Route::put('/update/{page}', 'update')->name('update');
        Route::delete('/delete/{page}', 'delete')->name('delete');
    });
});
