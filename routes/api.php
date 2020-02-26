<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/v1/products','ProductController');

Route::group(['prefix' => '/v1/profile/{userId}', 'middleware' => 'auth:api'], function() {
    Route::get('/orders', 'OrderController@index')->name('orders.index');
    Route::get('/orders/{orderId}', 'OrderController@show')->name('orders.show');
    Route::put('/orders/{orderId}', 'OrderController@update')->name('orders.cancel');
});


Route::group(['prefix' => '/v1/products'], function() {
    Route::apiResource('/{productId}/reviews', 'ReviewController');
    Route::post('/{productId}/orders', 'OrderController@store')->middleware('auth:api')->name('orders.store');
});

Route::post('/v1/register', 'AuthController@register');
Route::post('/v1/login','AuthController@login');
Route::post('/v1/logout','AuthController@logout')->middleware('auth:api');
