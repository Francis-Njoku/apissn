<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\PayController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/auth/list-users/', [UserController::class, 'listUsers']);
Route::post('/auth/refresh', [UserController::class, 'refresh']);
Route::post('/auth/register', [UserController::class, 'createUser']);
Route::post('/auth/logout', [UserController::class, 'logout']);
Route::post('/auth/forgot', [UserController::class, 'forgot']);
Route::post('/auth/reset', [UserController::class, 'reset']);
//Route::match(['get', 'post'], '/auth/login', [UserController::class, 'loginUser'])->name('login');
Route::post('/auth/login', [UserController::class, 'loginUser']);
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    #return redirect('/');
    return redirect()->route('home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post(
    '/forgot-password',
    [UserController::class, 'forgotPassword']
);
Route::post(
    '/reset-password',
    [UserController::class, 'resetPassword']
);

Route::get('/all/article/', [ArticleController::class, 'indexNoAuth']);
Route::get('/pay/callback/',  [PayController::class, 'handleGatewayCallback']);
Route::get('/article-single/{slug}', [ArticleController::class, 'showSingleArticle']);
Route::get('/media/', [ArticleController::class, 'listFiles']);


// No auth
Route::get('/plans/', [PlanController::class, 'index']);
Route::get('/no-auth-articles/', [ArticleController::class, 'indexNoAuth']);
Route::get('/latest-articles/', [ArticleController::class, 'getLatest']);
Route::get('/articles-by-media/', [ArticleController::class, 'indexByMediaType']);


Route::group(['middleware' => ['auth.jwt']], function () {
    Route::get('/auth/profile/', [UserController::class, 'profile']);
    Route::get('/user/attendance/', [UserAttendanceController::class, 'index']);
    Route::get('/manager/attendance/', [UserAttendanceController::class, 'userManagerAttendanceList']);
    Route::post('/auth/signout/', [UserController::class, 'signout']);
    Route::post('/user/clock/register/', [UserAttendanceController::class, 'userRegisterClock']);
    Route::post('/pay/',  [PayController::class, 'redirectToGateway']);
    Route::get('/pay/reference/{reference}',  [PayController::class, 'paymentReference']);
    Route::get('/pay/history/',  [PayController::class, 'paymentHistory']);
    Route::post('/store/ftm/', [ArticleController::class, 'store']);
    Route::get('/payment/status/', [PayController::class, 'paymentStatus']);
    Route::get('/generate/slug/', [ArticleController::class, 'newsletterGenerateSlug']);
    Route::get('/user/type/', [UserController::class, 'userStatus']);

});

Route::group(['middleware' => ['auth.jwt','admin' ]], function () {
    Route::post('/admin/create/user/', [UserController::class, 'adminCreateUser']);
    Route::post('/upload/file/', [ArticleController::class, 'uploadFile']);
    Route::get('/update/imagepath/', [ArticleController::class, 'updateImagePaths']);
    Route::put('/article/update/{slug}', [ArticleController::class, 'update']);
    Route::put('/article/status/update/{slug}', [ArticleController::class, 'updateStatus']);
});

Route::group(['middleware' => ['auth.jwt','payment' ]], function () {
    Route::post('/admin/create/user/', [UserController::class, 'adminCreateUser']);

});

Route::group(['middleware' => ['auth.jwt','subscribed' ]], function () {
    Route::get('/articles/', [ArticleController::class, 'index']);
    Route::get('/article/{id}', [ArticleController::class, 'show']);

});