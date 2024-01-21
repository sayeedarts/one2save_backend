<?php

/**
 * Admin grouping Routes
 * 
 * @author tanmayap
 * @date 19 nov 2020
 */

Route::get('dashboard', 'DashboardController@show')->name('admin-dashboard');
Route::post('notification/mark-all-read', 'SettingsController@markAllRead')->name('noti-mark-all-read');

// Services
Route::get('service/create', 'ServiceController@create')->name('service.create')->middleware(['permission:add-service']);
Route::get('service/sort', 'ServiceController@sort')->name('service.sort');
Route::post('service/sort/save', 'ServiceController@sortSave')->name('service.sort.save');
Route::post('service/store', 'ServiceController@store')->name('service.store');
Route::get('service/{id}/edit', 'ServiceController@create')->name('service.edit');
Route::post('service/update', 'ServiceController@update')->name('service.update');
Route::get('service/list', 'ServiceController@index')->name('service.list');
Route::get('service/{id}/delete', 'ServiceController@destroy')->name('service.delete');

// Gallery
Route::get('gallery/create', 'GalleryController@create')->name('gallery.create');
Route::post('gallery/store', 'GalleryController@store')->name('gallery.store');
Route::get('gallery/{id}/edit', 'GalleryController@create')->name('gallery.edit');
Route::post('gallery/update', 'GalleryController@update')->name('gallery.update');
Route::get('gallery/list', 'GalleryController@index')->name('gallery.list');
Route::get('gallery/{id}/delete', 'GalleryController@destroy')->name('gallery.delete');

// Files Manager
Route::get('file-manager/create', 'FileManagerController@create')->name('file-manager.create');
Route::post('file-manager/store', 'FileManagerController@store')->name('file-manager.store');
Route::get('file-manager/{id}/edit', 'FileManagerController@create')->name('file-manager.edit');
Route::post('file-manager/update', 'FileManagerController@update')->name('file-manager.update');
Route::get('file-manager/list', 'FileManagerController@index')->name('file-manager.list');
Route::get('file-manager/{id}/delete', 'FileManagerController@destroy')->name('file-manager.delete');

// Services Category
Route::get('service-categories/create', 'ServiceCategoriesController@create')->name('service-categories.create');
Route::post('service-categories/store', 'ServiceCategoriesController@store')->name('service-categories.store');
Route::get('service-categories/{id}/edit', 'ServiceCategoriesController@create')->name('service-categories.edit');
Route::post('service-categories/update', 'ServiceCategoriesController@update')->name('service-categories.update');
Route::get('service-categories/list', 'ServiceCategoriesController@index')->name('service-categories.list');
Route::get('service-categories/{id}/delete', 'ServiceCategoriesController@destroy')->name('service-categories.delete');

// Manage Storage Module
Route::get('storage/create', 'StorageController@create')->name('storage.create');
Route::post('storage/store', 'StorageController@store')->name('storage.store');
Route::get('storage/{id}/edit', 'StorageController@create')->name('storage.edit');
Route::post('storage/update', 'StorageController@update')->name('storage.update');
Route::get('storage/list', 'StorageController@index')->name('storage.list');
Route::get('storage/{id}/delete', 'StorageController@destroy')->name('storage.delete');

// packaging Module
Route::get('packaging/create', 'PackagingController@create')->name('packaging.create');
Route::post('packaging/store', 'PackagingController@store')->name('packaging.store');
Route::get('packaging/{id}/edit', 'PackagingController@create')->name('packaging.edit');
Route::post('packaging/update', 'PackagingController@update')->name('packaging.update');
Route::get('packaging/list', 'PackagingController@index')->name('packaging.list');
Route::get('packaging/{id}/delete', 'PackagingController@destroy')->name('packaging.delete');

// Page Route
Route::get('page/create', 'PageController@create')->name('page.create');
Route::post('page/post', 'PageController@store')->name('page.store');
Route::post('page/update', 'PageController@update')->name('page.update');
Route::get('page/list', 'PageController@list')->name('page.list');
Route::get('page/{id}/edit', 'PageController@create')->name('page.edit');
Route::get('page/{id}/delete', 'PageController@delete')->name('page.delete');

// User Pages
Route::get('user/list', 'UsersController@index')->name('user.list')->middleware('role:admin');
Route::get('user/create', 'UsersController@create')->name('user.create')->middleware('role:admin');
Route::post('user/post', 'UsersController@store')->name('user.store');
Route::get('user/{id}/edit', 'UsersController@create')->name('user.edit');
Route::post('user/{id}/update', 'UsersController@update')->name('user.update');
Route::get('user/{id}/delete', 'UsersController@destroy')->name('user.delete');

Route::get('quotes', 'UsersController@quoteRequests')->name('quotes.all');
Route::post('quotes/notify', 'UsersController@notifyUser')->name('quotes.notify');
Route::get('orders', 'OrderController@orderList')->name('orders.all');

// Role Pages
Route::get('role/list', 'RolesController@index')->name('role.list')->middleware('role:admin');
Route::get('role/create', 'RolesController@create')->name('role.create');
Route::post('role/post', 'RolesController@store')->name('role.store');
Route::get('role/{id}/edit', 'RolesController@create')->name('role.edit');
Route::post('role/{id}/update', 'RolesController@update')->name('role.update');
Route::get('role/{id}/delete', 'RolesController@destroy')->name('role.delete');

Route::get('settings/add', 'SettingsController@add')->name('settings.add');
Route::post('settings/store', 'SettingsController@store')->name('settings.store');

Route::resource('template', TemplateController::class);