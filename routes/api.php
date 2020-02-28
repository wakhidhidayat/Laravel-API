<?php

use Illuminate\Http\Request;

Route::group(['prefix' => '/v1/admin', 'middleware' => 'auth:api'], function() {
    Route::apiResource('/products', 'API\ProductController');
    Route::group(['prefix' => '/{userId}/orders'], function () {
        Route::get('/', 'API\OrderController@index');
        Route::get('/{orderId}/OrderController@show');
        Route::put('/{orderId}/OrderController@update');
    });
    Route::group(['prefix' => 'products/{productId}'], function () {
        Route::apiResource('/reviews', 'API\ReviewController');
    });
});

Route::group(['prefix' => '/v1'], function () {
    Route::get('/products', 'API\ProductController@index')->name('products.index');
    Route::get('/products/{productId}', 'API\ProductController@show')->name('products.show');
});

Route::group(['prefix' => '/v1/profile/{userId}', 'middleware' => 'auth:api'], function() {
    Route::get('/orders', 'API\OrderController@index')->name('orders.index');
    Route::get('/orders/{orderId}', 'API\OrderController@show')->name('orders.show');
    Route::put('/orders/{orderId}', 'API\OrderController@update')->name('orders.cancel');
});


Route::group(['prefix' => '/v1/products'], function() {
    Route::apiResource('/{productId}/reviews', 'API\ReviewController');
    Route::post('/{productId}/orders', 'API\OrderController@store')->middleware('auth:api')->name('orders.store');
});

Route::post('/v1/register', 'API\AuthController@register');
Route::post('/v1/login','API\AuthController@login');
Route::post('/v1/logout','API\AuthController@logout')->middleware('auth:api');
