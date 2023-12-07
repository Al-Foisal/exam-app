<?php

use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ExamManageController;
use App\Http\Controllers\Api\SubscribtionController;
use App\Http\Controllers\Api\TeacherPanelController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\UserProfileController;
use App\Models\CompanyInfo;
use App\Models\Exam;
use App\Models\Material;
use App\Models\Notification;
use App\Models\Page;
use App\Models\Subject;
use App\Models\TopicSource;
use App\Models\User;
use App\Models\Written;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/logout', function (Request $request) {
    $user = $request->user();
    $user->tokens()->delete();
    Auth::guard('web')->logout();

    return ['status' => true, 'message' => 'Logout Successful!'];
});

Route::middleware('auth:sanctum')->post('/contact-us', function (Request $request) {
    $data = CompanyInfo::find(1);

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});
Route::middleware('auth:sanctum')->post('/privacy-policy', function (Request $request) {

    $data = Page::where('slug', 'privacy-policy')->first();

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});
Route::middleware('auth:sanctum')->post('/store-fcm-token', function (Request $request) {

    $data            = User::find(Auth::id());
    $data->fcm_token = $request->fcm_token;
    $data->save();

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});
Route::middleware('auth:sanctum')->post('/notification', function (Request $request) {

    $data = Notification::where('user_id', Auth::id())->with('user', 'written')->orderBy('id', 'desc')->paginate();

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});
Route::middleware('auth:sanctum')->post('/make-notification-seen', function (Request $request) {

    $data = Notification::where('user_id', Auth::id())->with('user', 'written')->orderBy('id', 'desc')->paginate();

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});

Route::middleware('auth:sanctum')->post('/get-material', function (Request $request) {
    $data = Material::where('category', $request->category);

    if ($request->subject_id) {
        $data = $data->where('subject_id', 'LIKE', '%' . $request->subject_id . '%');
    }

    if ($request->search) {

// $subjects = Subject::where('name', 'LIKE', '%' . $request->search . '%')->pluck('id')->toArray();

// if ($subjects) {

//     $data = $data->orWhere('subject_id', 'LIKE', '%' . implode(',', $subjects) . '%');

// }

// $topic = TopicSource::where('topic', 'LIKE', '%' . $request->search . '%')->where('source', 'LIKE', '%' . $request->search . '%')->pluck('id')->toArray();

// if ($topic) {

//     return $topic;

//     $data = $data->orWhere('topic_id', 'LIKE', '%' . implode(',', $topic) . '%');
        // }

        $data = $data->where('name', 'LIKE', $request->search . '%');

    }

    $data = $data->latest()->paginate();

    foreach ($data as $item) {
        $item['subjects'] = Subject::whereIn('id', explode(',', $item->subject_id))->get();
        $item['sources']  = TopicSource::whereIn('id', explode(',', $item->topic_id))->get();
    }

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});
Route::middleware('auth:sanctum')->get('/get-present-live-exam', function (Request $request) {

    $data = [];

    $exam = Exam::whereDate('published_at', '<=', date('Y-m-d'))
        ->whereDate('expired_at', '>=', date('Y-m-d'))
        ->select(['id', 'category', 'subcategory', 'childcategory'])
        ->get();

    $written = Written::whereDate('published_at', '<=', date('Y-m-d'))
        ->whereDate('expired_at', '>=', date('Y-m-d'))
        ->select(['id', 'category', 'subcategory', 'childcategory'])
        ->get();

    $data['exam']    = $exam;
    $data['written'] = $written;

    return response()->json([
        'status' => true,
        'data'   => $data,
    ]);

});

Route::controller(UserAuthController::class)->prefix('/auth')->group(function () {
    Route::post('/register', 'register');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/login', 'login');
    Route::post('/store-forgot-password', 'storeForgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::post('/resend-otp', 'resendOTP');
});

Route::controller(SubscribtionController::class)->group(function () {
    Route::post('/packages', 'packages');
    Route::post('/purchase-package', 'purchasePackage');
    Route::post('/package-history', 'packageHistory');
});

Route::middleware('auth:sanctum')->controller(UserProfileController::class)->prefix('/profile')->group(function () {
    Route::get('/user', 'user');
    Route::post('/update', 'update');
    Route::post('/asking-query', 'askingQuery');
});

Route::middleware('auth:sanctum')->controller(ExamManageController::class)->prefix('/exam')->group(function () {
    Route::post('/check-live-exam', 'checkLiveExam');
    Route::post('/routine', 'routine');
    Route::post('/archive', 'archive');
    Route::post('/syllabus', 'syllabus');
    Route::post('/archive-exam-question-details', 'archiveExamQuestionDetails');
    Route::post('/toggle-favorite', 'toggleFavorite');
    Route::post('/favorite-list', 'favoriteList');
    Route::post('/result-list', 'resultList');
    Route::post('/subject-list', 'subjectList');
    Route::post('/merit-list', 'meritList');
});

Route::middleware('auth:sanctum')->controller(AnswerController::class)->prefix('/answer')->group(function () {
    Route::post('/store-preliminary-answer', 'storePreliminaryAnswer');
    Route::post('/show-preliminary-answer', 'showPreliminaryAnswer');
    Route::post('/preliminary-answer-script', 'preliminaryAnswerScript');
    Route::post('/preliminary-answer-merit-list', 'preliminaryAnswerMeritList');

    Route::post('/store-written-answer', 'storeWrittenAnswer');
});

Route::middleware('auth:sanctum')->controller(TeacherPanelController::class)->prefix('/teacher')->group(function () {
    Route::post('/exam-and-paper', 'examAndPaper');
    Route::post('/store-exam-paper-assessment', 'storeExamPaperAssessment');

    //wallet
    Route::post('/wallet', 'wallet');
    Route::post('/withdrawal-request', 'withdrawalRequest');

    Route::post('/dashboard', 'dashboard');
});

Route::get('/category', function () {
    return [
        'BCS'    => [
            'Preliminary',
            'Written',
        ],
        'Bank'   => [
            'Preliminary',
            'Written',
        ],
        'Others' => [
            'Preliminary' => [
                'Primary',
                '11 to 20 Grade',
                'Non-Cadre',
                'Job Solution',
            ],
            'Written'     => [
                'Job Solution',
            ],
        ],
        'Free'   => [
            'Preliminary' => [
                'Weekly',
                'Daily',
            ],
            'Written'     => [
                'Weekly',
            ],
        ],
        'Recent',
        'Record Class',
    ];
});
