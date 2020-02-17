<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/v1/products','ProductController');

Route::group(['prefix' => '/v1/products'], function() {
    Route::apiResource('/{productId}/reviews', 'ReviewController');
});

Route::post('/v1/register', 'AuthController@register');
Route::post('/v1/login','AuthController@login');
Route::post('/v1/logout','AuthController@logout');
