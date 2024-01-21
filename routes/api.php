<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::post('signin', 'App\Http\Controllers\Api\AuthController@login');
// Route::post('signup', 'App\Http\Controllers\Api\AuthController@signup');
Route::post('otp-send', 'App\Http\Controllers\Api\AuthController@sendOtp');
Route::post('otp-login', 'App\Http\Controllers\Api\AuthController@optSignin');
Route::post('signup', 'App\Http\Controllers\Api\AuthController@signup');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authorized Routes
Route::middleware('auth:sanctum')->namespace('App\Http\Controllers\Api')->group(function () {
    // Users Protected Routes
    Route::post('/user/details', 'AuthController@userDetails');
    Route::post('/user/update', 'AuthController@updateUserDetails');
    Route::post('/user/password', 'AuthController@updatePassword');
    Route::post('/user/address', 'CommonController@updateAddress');
    Route::post('/user/address/get', 'CommonController@getAddress');
    
    Route::post('/orders', 'OrderController@list');
    Route::post('/orders/{id}/details', 'OrderController@getOrder');
});

// Un-authorized Routes
Route::namespace('App\Http\Controllers\Api')->group(function () {
    Route::get('/home/masters', 'CommonController@getHomeMasters');
    Route::get('/site-settings', 'CommonController@getSiteSettings');
    Route::get('/site-imp-settings', 'CommonController@getImpSettings');
    Route::get('/site/refresh-state', 'CommonController@refreshState');
    Route::get('/site/reset-refresh-state', 'CommonController@updateRefreshState');
    Route::get('/blogs', 'CommonController@blogs');
    Route::get('/blogs/slugs', 'CommonController@getBlogSlugs');
    Route::get('/blogs/{slug}', 'CommonController@getBlogDetails');
    Route::get('/page/{slug}', 'CommonController@pageDetails');
    Route::get('/page-slug-list', 'CommonController@pageSlugs');
    Route::get('/service/pickup-options', 'CommonController@pickupOptions');
    Route::get('/service/additional-helps', 'CommonController@serviceAdditionalHelps');
    Route::get('/instagram/feeds', 'CommonController@instaFeeds');
    Route::get('/service/all', 'ServiceController@service');
    Route::get('/service/slugs', 'ServiceController@getServiceSlugs');
    Route::get('/service/slugs/with-child', 'ServiceController@getServiceDetailedSlugs');
    Route::get('/service/slugs/{service}/category', 'ServiceController@getServiceCategorySlugs');
    Route::get('/service/{id}/details', 'ServiceController@serviceDetails');
    Route::get('/service/{id}/info', 'ServiceController@shortServiceDetails');
    Route::get('/service/items', 'ServiceController@getAllServiceItems');
    Route::get('/service-category/{slug}/details', 'ServiceController@serviceCategoryDetails');
    Route::post('/quote-request', 'ServiceController@storeQuoteRequest');
    Route::post('/quote/requests', 'CommonController@quoteRequests');
    Route::get('/quote-request/{id}/details', 'ServiceController@quoteRequestDetails');
    Route::get('/galleries', 'CommonController@galleries');
   
    // Storage API
    Route::get('/storages', 'StorageController@storage');
    Route::get('/storages/{id}/details', 'StorageController@storageDetails');
    Route::post('/storage/payment', 'StorageController@storagePayment');
    
    // Packaging
    Route::get('/packagings', 'PackagingController@packagings');
    Route::get('/packaging/{id}/details', 'PackagingController@packaging');
    Route::get('/packaging/ids', 'PackagingController@packageIds');
    Route::post('/packaging/payment', 'PackagingController@packingPayment');
    
    Route::post('/contact-us', 'CommonController@saveContactUs');
    Route::get('/countries', 'CommonController@countries');
    Route::get('/cities', 'CommonController@cities');
    Route::get('/email/send', 'CommonController@sendEmail');
    Route::get('/sms/send', 'CommonController@sendSms');

    // User Routes
    // Route::post('/user/details', 'AuthController@userDetails');
});