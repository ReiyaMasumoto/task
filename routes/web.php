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
Auth::routes();

//商品一覧画面
Route::get('/home', 'ProductController@index')->name('home');

//商品一覧画面の商品削除
Route::post('/product/delete/{id}', 'ProductController@destroy')->name('destroy');

//商品一覧画面の商品検索
Route::get('/product/search', 'ProductController@search')->name('search');

//商品登録画面
Route::get('/product/create', 'ProductController@create')->name('create');

//商品登録処理
Route::post('/product/store', 'ProductController@store')->name('store');

//商品詳細画面
Route::get('/product/{id}', 'ProductController@show')->name('show');

//商品編集画面
Route::get('/product/edit/{id}', 'ProductController@edit')->name('edit');

//商品更新処理
Route::put('/product/update', 'ProductController@update')->name('update');




