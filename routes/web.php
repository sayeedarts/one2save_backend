<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::get('/', function () {
    echo "Welcome to One Place to Save";
    exit;
});

// language translator
Route::get('locale/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
});

/**
 * Public Routes
 */
Route::get('/admin/login', 'App\Http\Controllers\Site\LoginController@adminLoginShow')->name('admin.login');
Route::post('/admin/login/post', 'App\Http\Controllers\Site\LoginController@adminLoginPost')->name('admin.login.post');
Route::get('/user/login', 'App\Http\Controllers\Site\LoginController@show')->name('user.login');
Route::post('/user/login/post', 'App\Http\Controllers\Site\LoginController@doLogin')->name('user.login.post');
Route::get('/user/logout', 'App\Http\Controllers\Site\LoginController@logout')->name('user.logout');

// Route::get('/service/quote', 'App\Http\Controllers\Site\ServiceQuoteController@makeQuote')->name('service.quote');
Route::get('/service/{id}/quote-generate', 'App\Http\Controllers\Site\ServiceQuoteController@generatePdf')->name('service.quote.pdf');
Route::get('/invoice/{id}/generate', 'App\Http\Controllers\Site\ServiceQuoteController@generateOrderInvoice')->name('order.invoice');

// Public Routes
Route::namespace('App\Http\Controllers\Site')
    ->group(__DIR__ . '/public.php');


Route::get('/home-page', \App\Http\Livewire\Home::class);

Route::middleware(['auth:web'])->group(function () {
});

/**
 * Admin Routes
 */
Route::prefix('admin')
    ->middleware(['auth:admin', 'verified'])
    ->namespace('App\Http\Controllers\Admin')
    ->group(__DIR__ . '/admin.php');

// Ajax Requests
Route::prefix('admin')->middleware(['auth:web', 'verified'])->namespace('App\Http\Controllers')->group(function () {
    Route::post('storage-images/delete', 'Admin\StorageController@deleteImages')->name('storage-images.delete');
});
