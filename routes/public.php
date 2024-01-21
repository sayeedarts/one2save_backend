<?php 
/**
 * Public grouping Routes
 * 
 * @author tanmayap
 * @date 19 nov 2020
 */
// email verification
Route::get('/email-verify/send', 'Auth\RegisterController@sendEmailVerification')->name('email.verify');
Route::get('/email-verify/check/{token}', 'Auth\RegisterController@emailVerificationCheck')->name('email.verify.check');

Route::get('/forgot-password', 'Auth\RegisterController@forgotPassword')->name('forgot.password');
Route::post('/forgot-password', 'Auth\RegisterController@forgotPasswordSend')->name('forgot.password.send');
Route::get('/forgot-password/verify', 'Auth\RegisterController@verifyForgotPassword')->name('forgot.password.verify');
Route::post('/forgot-password/verify', 'Auth\RegisterController@verifyForgotPassword')->name('forgot.password.verify');
// Route::get('/forgot-password/change', 'Auth\RegisterController@verifyForgotPassword')->name('forgot.password.change');

Route::get('/register-patient', 'Auth\RegisterController@show')->name('register-patient');
Route::post('/register-patient-post', 'Auth\RegisterController@addPatient')->name('add.patient');
Route::post('/register-patient-bymrn-post', 'Auth\RegisterController@addPatientByMrn')->name('add.patient.bymrn');
Route::get('/departments', 'ListingsController@departments')->name('departments');
Route::get('/department/{slug}', 'ListingsController@department')->name('department.details');
Route::get('/doctor/{slug}', 'ListingsController@doctor')->name('doctor.details');
Route::get('/doctors', 'ListingsController@doctors')->name('public.doctors.list');
Route::get('/hospital/{slug}', 'ListingsController@hospital')->name('hospital.show');
Route::get('/page/{slug}', 'ListingsController@page')->name('page.details');
Route::get('/media/images', 'ListingsController@imagesList')->name('images.list');
Route::get('/media/images/{id}/more', 'ListingsController@imagesList')->name('images.more');
Route::get('/media/videos', 'ListingsController@videosList')->name('videos.list');
Route::get('/news-events/all', 'ListingsController@newsEvents')->name('events.list');
Route::get('/news-events/{id}/details', 'ListingsController@newsEventDetails')->name('events.details');
Route::get('/bmi-check', 'ListingsController@bmiCheck')->name('bmi-check');

// Synchronise Database 
Route::get('/update-patients-to-oracle/{type}', 'SyncController@uploadPatients')->name('sync.patients.up');
Route::get('/update-mrns-from-oracle', 'SyncController@getMrnNumbers')->name('sync.mrn.down');

Route::get('/search', 'SearchController@search')->name('public.search');