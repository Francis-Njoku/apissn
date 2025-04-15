<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PremiumArticleController;
use App\Http\Controllers\SsnArticleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\dash\AdminController;
use App\Http\Controllers\dash\AdminForumController;
use App\Http\Controllers\dash\ModeratorController;
use App\Http\Controllers\dash\SubscriberController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Auth\ForgotController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetController;
use App\Http\Controllers\Auth\VerificationController;

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

/*Route::get('/', function () {
    return view('welcome');
});*/


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/default', function () {
    return view('welcome');
});
Route::group(['middleware' => ['auth']], function() {
    /**
    * Logout Route
    */
    Route::get('/signout', 'Auth\LogoutController@perform')->name('logout.perform');
 });

Auth::routes();

Route::get('/home2', 'HomeController@index')->name('home99');
Route::get('/forum', 'ForumController@forum')->name('forum');
Route::post('/signout2', [admin\DashboardController::class, 'logout'])->name('signout');

Route::get('/home2',  [HomeController::class, 'testing'])->name('home67');

Route::post('/pay',  [PaymentController::class, 'redirectToGateway'])->name('pay');
Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback']);
Route::get('/admin/user_pay', 'admin\DashboardController@user_pay')->name('Dashboard');
Route::get('/pay-test', [SubscriptionController::class, 'index'])->name('home23');
Route::get('/news-test', [SubscriptionController::class, 'newslet'])->name('home90');
Route::get('/', [LandController::class, 'index'])->name('homer');
/*
Route::get('/register', 'LandController@index')->name('homer444');
*/
Route::get('/plan', [LandController::class, 'plan'])->name('plan');
Route::get('/pricing', [LandController::class, 'pricing'])->name('pricing');

Route::get('/stock-select/pricing', [LandController::class, 'stock_pricing'])->name('stock-pricing');

/*Route::get('/crypto/pricing', 'LandController@crypto_pricing')->name('crypto_pricing');
*/
Route::get('/agrotech/pricing', [LandController::class, 'agrotech_pricing'])->name('agrotech-pricing');
Route::get('/premium-article/pricing', [LandController::class, 'premium_article_pricing'])->name('premium_article_pricing');
Route::get('/home', [LandController::class, 'index'])->name('homer');
Route::get('/check', [PaymentController::class, 'index'])->name('home11');
Route::get('/tenks', [LandController::class, 'thank'])->name('home11');
Route::get('/disclaimer', [LandController::class, 'terms'])->name('home11');
Route::get('/thank-you', [LandController::class, 'thank_you'])->name('thank-you');

// Admin

Route::get('/admin-ui', [AdminController::class, 'index'])->name('admin');
Route::get('/admin', [AdminController::class, 'add_newsletter'])->name('admin-add');
Route::post('/admin-stock-store', [AdminController::class, 'store_newsletter'])->name('admin-store');
Route::get('/admin-stock-add', [AdminController::class, 'add_newsletter'])->name('admin-add');
Route::get('/admin-newsletter-list', [AdminController::class, 'list_newsletter'])->name('admin-list');
//Route::get('/admin-newsletter/{slug}', ['as' => 'admin.single', 'uses' => [AdminController::class, 'newsletter']])
//    ->where('slug', '[\w\d\-\_]+');
Route::get('/admin-newsletter/{slug}', [AdminController::class, 'newsletter'])->name('admin.single');           

//Route::get('/admin-newsletter/edit/{slug}', ['as' => 'admin.single', 'uses' => [AdminController::class, 'edit_newsletter']])
//    ->where('slug', '[\w\d\-\_]+');  
Route::get('/admin-newsletter/edit/{slug}', [AdminController::class, 'edit_newsletter'])->name('admin.single');           
Route::post('/update-newsletter', [AdminController::class, 'update_newsletter'])->name('update_newsletter');
Route::get('/admin/list-users', [AdminController::class, 'listUsers']);
Route::get('/admin/list-users/pagination', [AdminController::class, 'fetch_data']);
Route::get('/admin/list-subscribers', [AdminController::class, 'list_subscribers']);
Route::get('/admin/list-subscribers/pagination', [AdminController::class, 'fetch_subscribers']);
Route::get('/admin/delist', [AdminController::class, 'delist_subscriber']);
Route::get('/admin/forum/category', [AdminForumController::class, 'category']);
Route::get('/admin/forum/category/pagination', [AdminForumController::class, 'fetch_category']);
Route::post('/admin/forum/store-category', [AdminForumController::class, 'store_category'])->name('admin-store-category');
Route::post('/admin/forum/update-category', [AdminForumController::class, 'update_cateogry'])->name('admin-update-category');
 
Route::get('/admin/article/add', [AdminController::class, 'add_article']);
Route::post('/admin/article/store', [AdminController::class, 'store_article'])->name('store-article');
Route::get('/admin/articles', [AdminController::class, 'list_articles']);
//Route::get('/admin/article/edit/{id}', ['as' => 'admin.article-edit.single', 'uses' => [AdminController::class, 'edit_article']])
//    ->where('id', '[\w\d\-\_]+');
Route::get('/admin/article/edit/{id}', [AdminController::class, 'edit_article'])->name('admin.article-edit.single');           

Route::post('/admin/article/update', [AdminController::class, 'update_article'])->name('update-article');
//Route::get('/admin/article/{slug}', ['as' => 'admin.article.single', 'uses' => [AdminController::class, 'article']])
//    ->where('slug', '[\w\d\-\_]+');  
Route::get('/admin/article/{slug}', [AdminController::class, 'article'])->name('admin.article.single');           
Route::get('/admin/forum/create-topic', [AdminForumController::class, 'create_topic']);
Route::post('/admin/forum/topic/store', [AdminForumController::class, 'store_topic'])->name('admin-store-topic');
//Route::get('/admin/forum/{category}/{slug}', ['as' => 'admin-topic.single', 'uses' => [AdminForumController::class, 'topic']])
//    ->where('slug', '[\w\d\-\_]+');
Route::get('/admin/forum/{category}/{slug}', [AdminForumController::class, 'topic'])->name('admin-topic.single');           

//Route::get('/admin/forum-edit-topic/{id}', ['as' => 'admin-topic-edit', 'uses' => [AdminForumController::class, 'edit_topic']])
//    ->where('id', '[\w\d\-\_]+');
Route::get('/admin/forum-edit-topic/{id}', [AdminForumController::class, 'edit_topic'])->name('admin-topic-edit');           

Route::get('/admin/forum/topics', [AdminForumController::class, 'topics']);
Route::get('/admin/topic/pagination', [AdminForumController::class, 'fetch_topics']);
Route::post('/admin/forum/topic/update', [AdminForumController::class, 'update_topic'])->name('admin-update-topic');
    

// Members
Route::get('/forum/create-topic', [SubscriberController::class, 'create_topic']);
Route::post('/forum/topic/store', [SubscriberController::class, 'store_topic'])->name('store-topic');
Route::get('/forum/{category}/{slug}', ['as' => 'topic.single', 'uses' => [SubscriberController::class, 'topic']])
    ->where('slug', '[\w\d\-\_]+');
//Route::get('/article/{slug}', ['as' => 'article.single', 'uses' => [SubscriberController::class, 'show_article']])
//    ->where('slug', '[\w\d\-\_]+');  
Route::get('/article/{slug}', [SubscriberController::class, 'show_article'])->name('article.single');           

//Route::get('/ssn/{slug}', ['as' => 'ssn.single', 'uses' => [SubscriberController::class, 'show_ssn']])
//    ->where('slug', '[\w\d\-\s\_]+');         
Route::get('/ssn/{slug}', [SubscriberController::class, 'show_ssn'])->name('ssn.single');           


// Users
Route::get('/get-agrotech-newsletter', [LandController::class, 'news_agrotech'])->name('Agrotech-newsletter');
Route::get('/get-newsletter', [LandController::class, 'news_book'])->name('Deal-book');
//Route::get('/get-newsletter/{slug}', ['as' => 'ssn-newsletter.single', 'uses' => [LandController::class, 'news_single_newsletter']])
//    ->where('slug', '[\w\d\-\s\_]+');   
Route::get('/get-newsletter/{slug}', [LandController::class, 'news_single_newsletter'])->name('ssn-newsletter.single');           

/*Route::get('/get-crypto-newsletter', 'LandController@news_crypto')->name('news-crypto');
*/
Route::post('/send-newsletter', [LandController::class, 'send_news_book'])->name('send_deal_book');
/*Route::post('/send-crypto-newsletter', 'LandController@send_crypto_book')->name('send_crypto_book');
*/
Route::get('/premium-article', [PremiumArticleController::class, 'article'])->name('premium_article');
Route::get('/premium-article-pagination', [PremiumArticleController::class, 'fetch_articles'])->name('premium_article_pagination');
Route::get('/get-pdf', [HomeController::class, 'generate_pdf'])->name('generate-pdf');

Route::get('/ssn', [SsnArticleController::class, 'newsletter'])->name('ssn');
Route::get('/ssn-pagination', [SsnArticleController::class, 'fetch_ssn'])->name('ssn_article_pagination');


Route::get('/admin/site-featued-image', [AdminController::class, 'addFeaturedImage'])->name('admin-featured-image');
Route::post('/admin/store-featured-image', [AdminController::class, 'storeFeaturedImage'])->name('admin-store-featured-image');
Route::get('/featued-image', 'LandController@FeaturedImage')->name('featured-image');
Route::get('/builder', [HomeController::class, 'formbuilder'])->name('builder');
Route::post('/builder/store', [HomeController::class, 'postBuilder'])->name('post-builder');

#test
Route::get('/v4', 'HomeController@v4')->name('version-4');

Route::get('/debug-env', function () {
    dd([
        'MAILCHIMP_APIKEY' => env('MAILCHIMP_APIKEY'),
        'MAILCHIMP_SERVER' => env('MAILCHIMP_SERVER'),
    ]);
});