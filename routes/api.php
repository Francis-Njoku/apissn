<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\PayController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\ArticleController;

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


// No auth
Route::get('/articles/sample/', [ArticleController::class, 'sampleArticle']);
Route::get('/plans/', [PlanController::class, 'index']);
Route::prefix('auth')->group(function () {
    Route::post('/refresh', [UserController::class, 'refresh']);
    Route::post('/register', [UserController::class, 'createUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/forgot', [UserController::class, 'forgot']);
    Route::post('/reset', [UserController::class, 'reset']);
    Route::post('/login', [UserController::class, 'loginUser']);
    Route::post(
        '/forgot-password',
        [UserController::class, 'forgotPassword']
    );
    Route::post(
        '/reset-password',
        [UserController::class, 'resetPassword']
    );
});

// auth
Route::prefix('email')->group(function () {
    Route::get('/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');
    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        #return redirect('/');
        return redirect()->route('home');
    })->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});
Route::group(['middleware' => ['auth.jwt']], function () {
    Route::prefix('auth')->group(function () {
        Route::post('/signout/', [UserController::class, 'signout']);
        Route::get('/profile/', [UserController::class, 'profile']);
    });
    Route::prefix('pay')->group(function () {
        Route::post('/', [PayController::class, 'redirectToGateway']);
        Route::get('/callback/', [PayController::class, 'handleGatewayCallback']);
        Route::get('/reference/{reference}', [PayController::class, 'paymentReference']);
        Route::get('/history/', [PayController::class, 'paymentHistory']);
        // Route::get('/payment/status/', [PayController::class, 'paymentStatus']);
        Route::get('/status/', [PayController::class, 'paymentStatus']);
    });
    Route::post('/store/ftm/', [ArticleController::class, 'store']);
    Route::get('/generate/slug/', [ArticleController::class, 'newsletterGenerateSlug']);
    Route::get('/user/type/', [UserController::class, 'userStatus']);
});
Route::group(['middleware' => ['auth.jwt', 'subscribed']], function () {
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/all/', [ArticleController::class, 'indexNoAuth']);
        Route::get('/latest/', [ArticleController::class, 'getLatest']);
        Route::get('/by-media/', [ArticleController::class, 'indexByMediaType']);
        Route::get('/{id}', [ArticleController::class, 'show']);
        Route::get('/{slug}/', [ArticleController::class, 'showSingleArticle']);
    });

});

Route::prefix('admin')->middleware(['auth.jwt', 'admin'])->group(function () {
    // Media routes
    Route::prefix('media')->group(function () {
        Route::get('/', [ArticleController::class, 'listFiles']);
        Route::post('/upload', [ArticleController::class, 'uploadFile']);
    });

    // Article management routes
    Route::prefix('articles')->group(function () {
        Route::post('/add', [ArticleController::class, 'storeArticle']);
        Route::put('/update/{slug}', [ArticleController::class, 'update']);
        Route::put('/status/update/{slug}', [ArticleController::class, 'updateStatus']);
        Route::get('/update-image-paths', [ArticleController::class, 'updateImagePaths']);
    });

    // User management routes
    Route::prefix('users')->group(function () {
        Route::post('/create', [UserController::class, 'adminCreateUser']);
        Route::get('/list', [UserController::class, 'listUsers']);
    });
});
