<?php

use App\Http\Controllers\Backend\AdminAuthenticationController;
use App\Http\Controllers\Backend\AdminManagementController;
use App\Http\Controllers\Backend\CompanyInfoController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ExamController;
use App\Http\Controllers\Backend\MaterialController;
use App\Http\Controllers\Backend\PackageController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\SubjectController;
use App\Http\Controllers\Backend\TeacherExamAssignController;
use App\Http\Controllers\Backend\TeacherManagementController;
use App\Http\Controllers\Backend\TopicSourceController;
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

    Route::controller(TopicSourceController::class)->prefix('/topic-source')->name('topic.source.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{topic_source}', 'edit')->name('edit');
        Route::put('/update/{topic_source}', 'update')->name('update');
        Route::delete('/delete/{topic_source}', 'delete')->name('delete');
    });

    Route::controller(ExamController::class)->prefix('/exam')->name('exam.')->group(function () {
        /**
         * preliminary
         */
        Route::get('/index', 'index')->name('index');
        Route::get('/create/{exam_id?}', 'create')->name('create');
        Route::any('/store-or-update/{exam_id?}', 'storeOrUpdate')->name('storeOrUpdate');

        Route::get('/mcq-question/{exam_id}', 'mcqQuestion')->name('mcqQuestion');
        Route::post('/create-or-update-mcq-question/{exam_id}', 'createOrUpdateMCQQuestion')->name('createOrUpdateMCQQuestion');
        Route::get('/delete-question/{question_id}', 'deleteQuestion')->name('deleteQuestion');

        /**
         * written
         */
        Route::get('/written', 'written')->name('written');
        Route::get('/written-create/{written_id?}', 'writtenCreate')->name('writtenCreate');
        Route::any('/written-store-or-update/{written_id?}', 'writtenStoreOrUpdate')->name('writtenStoreOrUpdate');

        Route::get('/written-question/{written_id}', 'writtenQuestion')->name('writtenQuestion');
        Route::post('/create-or-update-written-question/{written_id}', 'createOrUpdateWrittenQuestion')->name('createOrUpdateWrittenQuestion');
        Route::get('/delete-written-question/{question_id}', 'deleteWrittenQuestion')->name('deleteWrittenQuestion');

        /**
         * syllabus
         */
        Route::get('/syllabus', 'syllabus')->name('syllabus');
        Route::post('/upload-syllabus', 'uploadSyllabus')->name('uploadSyllabus');

        Route::post('/get-topic', 'getTopic')->name('getTopic'); //ajax request
    });

    Route::controller(MaterialController::class)->prefix('/material')->name('material.')->group(function () {
        Route::get('/index', 'index')->name('index');
        Route::get('/create/{material_id?}', 'create')->name('create');
        Route::any('/store-or-update/{material_id?}', 'storeOrUpdate')->name('storeOrUpdate');
        Route::post('/get-topic', 'getTopic')->name('getTopic'); //ajax request
    });

    /**
     * teacher section
     */
    Route::prefix('/teacher')->name('teacher.')->group(function () {
        Route::controller(TeacherManagementController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create-or-edit/{id?}', 'createOrEdit')->name('createOrEdit');
            Route::post('/store-or-update/{id?}', 'storeOrUpdate')->name('storeOrUpdate');
            Route::get('/show/{id}', 'show')->name('show');

            //wallet
            Route::get('/withdrawal-request', 'withdrawalRequest')->name('withdrawalRequest');
            Route::post('/update-withdrawal-request/{id}', 'updateWithdrawalRequest')->name('updateWithdrawalRequest');
        });

        Route::controller(TeacherExamAssignController::class)->prefix('/written')->name('written.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/assign-paper/{written_id}/{category}', 'assignPaper')->name('assignPaper');
            Route::post('/store-assign-paper', 'storeAssignPaper')->name('storeAssignPaper');
            Route::get('/removed-assign-teacher/{id}', 'removedAssignTeacher')->name('removedAssignTeacher');
        });
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

    Route::controller(PackageController::class)->prefix('/packages')->name('packages.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create-or-edit/{id?}', 'createOrEdit')->name('createOrEdit');
        Route::post('/store-or-update/{id?}', 'storeOrUpdate')->name('storeOrUpdate');
        Route::delete('/delete/{id}', 'destroy')->name('delete');
    });
});
