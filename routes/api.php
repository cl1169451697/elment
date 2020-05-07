<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['namespace' => 'Api'], function () {

    //后端echarts接口
    //商城
    Route::get('sales_count', 'VisualizationController@sales_count');
    Route::get('sales_amount', 'VisualizationController@sales_amount');
    Route::get('top', 'VisualizationController@top');
    Route::get('sales_area', 'VisualizationController@sales_area');

    //会员性别统计
    Route::get('sex_count', 'VisualizationController@sex_count');

    //会员省份统计
    Route::get('customer_province', 'VisualizationController@customer_province');
});



//移动端接口
Route::group(['namespace' => 'Api\Mobile'], function () {
    Route::get('/auth', 'AuthenticateController@authenticate');

    Route::get('/', 'IndexController@index');
    Route::get('/category', 'ProductController@category');
    Route::get('/product/list', 'ProductController@index');
    Route::get('/product/view', 'ProductController@show');
    Route::post('/register', 'CustomerController@register');
    Route::post('/login', 'CustomerController@login');


    Route::group(['middleware' => ['client.credentials']], function () {

        //购物车
        Route::group(['prefix' => 'cart'], function () {
            Route::post('/', 'CartController@store');
            Route::get('/', 'CartController@index');
            Route::delete('/', 'CartController@destroy');
            Route::patch('/', 'CartController@change_num');
        });

        Route::group(['prefix' => 'order'], function () {
            //下单
            Route::get('checkout', 'OrderController@checkout');

            //生成订单,支付
            Route::post('/', 'OrderController@store');
            Route::get('pay/{id}', 'OrderController@pay');

            //删除订单
            Route::delete('{id}', 'OrderController@destroy');

            //我的订单
            Route::get('{id}', 'OrderController@show');

            //首页
            Route::get('/', 'OrderController@index');
        });


        Route::group(['prefix' => 'address'], function () {
            //管理地址
            Route::get('/manage', 'AddressController@manage');
            //改变默认地址
            Route::patch('/', 'AddressController@default_address');
        });
        Route::resource('address', 'AddressController');
    });

});