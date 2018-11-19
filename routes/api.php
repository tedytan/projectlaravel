<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('users','Api\UserController@users');
Route::get('user/{id}','Api\UserController@user');
Route::get('logout/{id}','Api\UserController@logout');

Route::post('auth/login','Api\UserController@login');
Route::post('auth/register','Api\UserController@register');

Route::get('products','Api\ProductsController@products');
Route::get('product/{id}','Api\ProductsController@product');