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
//For search images uploads
Route::get('/uploaded_images','ImagesearchController@image_search_index')->name('search.index');

Route::get('/upload-search-images','ImagesearchController@image_search_add')->name('search.image');
Route::post('/search-images/store','ImagesearchController@image_search_store')->name('imgstore');

Route::post('/imagesearch','ImagesearchController@store')->name('imageuploadseach');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
