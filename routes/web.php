<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/**
 * 后台
 */
Route::namespace('Admin')->prefix('admin')->middleware('auth')->group(function () {
	
	Route::namespace('Ads')->prefix('ads')->group(function () {
	    //广告栏目
	    Route::group(['prefix' => 'categories'], function () {
	        Route::patch('sort_order', 'CategoriesController@sort_order')->name('categories.sort_order');
	        Route::patch('is_something', 'CategoriesController@is_something')->name('categories.is_something');
	    });
	    Route::resource('categories', 'CategoriesController');

	    //广告
	    Route::group(['prefix' => 'ads'], function () {
	        Route::patch('sort_order', 'AdsController@sort_order')->name('ads.sort_order');
	        Route::delete('destroy_checked', 'AdsController@destroy_checked')->name('ads.destroy_checked');
	        Route::patch('is_something', 'AdsController@is_something')->name('ads.is_something');

	        //回收站
	        Route::get('trash', 'AdsController@trash')->name('ads.trash');
	        Route::get('{ad}/restore', 'AdsController@restore')->name('ads.restore');
	        Route::delete('{ad}/force_destroy', 'AdsController@force_destroy')->name('ads.force_destroy');
	        Route::delete('force_destroy_checked', 'AdsController@force_destroy_checked')->name('ads.force_destroy_checked');
	        Route::post('restore_checked', 'AdsController@restore_checked')->name('ads.restore_checked');
	    });
	    Route::resource('ads', 'AdsController');
	});
	
    Route::get('/', 'HomeController@index')->name('admin.home');
    Route::post('photo', 'PhotoController@upload');

    //商城
    Route::namespace('Shop')->prefix('shop')->group(function () {

        //品牌
        Route::prefix('brands')->group(function () {
            Route::patch('sort_order', 'BrandsController@sort_order')->name('brand.sort_order');
            Route::patch('is_something', 'BrandsController@is_something')->name('brand.is_something');
        });
        Route::resource('brands', 'BrandsController');

        //分类
        Route::prefix('categories')->group(function () {
            Route::patch('sort_order', 'CategoriesController@sort_order')->name('category.sort_order');
            Route::patch('is_something', 'CategoriesController@is_something')->name('category.is_something');
        });
        Route::resource('categories', 'CategoriesController');

        //会员管理
        Route::resource('customers', 'CustomersController');

        //商品管理
        Route::group(['prefix' => 'products'], function () {
            Route::patch('change_stock', 'ProductsController@change_stock')->name('products.change_stock');
            Route::delete('destroy_checked', 'ProductsController@destroy_checked')->name('products.destroy_checked');
            Route::delete('destroy_gallery', 'ProductsController@destroy_gallery')->name('products.destroy_gallery');
            Route::patch('is_something', 'ProductsController@is_something')->name('products.is_something');

            //回收站
            Route::get('trash', 'ProductsController@trash')->name('products.trash');
            Route::get('/{products}/restore', 'ProductsController@restore')->name('products.restore');
            Route::delete('/{products}/force_destroy', 'ProductsController@force_destroy')->name('products.force_destroy');
            Route::post('force_destroy_checked', 'ProductsController@force_destroy_checked')->name('products.force_destroy_checked');
            Route::post('restore_checked', 'ProductsController@restore_checked')->name('products.restore_checked');
        });
        Route::resource('products', 'ProductsController');


        //订单管理
        Route::group(['prefix' => 'orders'], function () {
            Route::patch('picking', 'OrdersController@picking')->name('orders.picking');
            Route::patch('shipping', 'OrdersController@shipping')->name('orders.shipping');
            Route::patch('finish', 'OrdersController@finish')->name('orders.finish');
        });
        Route::resource('orders', 'OrdersController');

        //物流管理
        Route::prefix('expresses')->group(function () {
            Route::patch('sort_order', 'ExpressesController@sort_order')->name('expresses.sort_order');
            Route::patch('is_something', 'ExpressesController@is_something')->name('expresses.is_something');
        });
        Route::resource('expresses', 'ExpressesController');
    });
});


