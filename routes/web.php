<?php

use Illuminate\Support\Facades\Route;

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
    return redirect()->route("home");
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource("/admin/users", "AdminUsersController");
Route::get("/user/{nombre}/search", "UserController@search");
Route::get("/user/{nombre}/advancedSearch", "UserController@advancedSearch");
Route::post("/user/{nombre}/advancedSearch", "UserController@advancedSearch");
Route::post("/user/{nombre}/download/{type}/{resourceName}", "UserController@download");
Route::get("/user/{nombre}", "UserController@index")->name("userIndex");
//si le quito el show no funciona el like, despues de los parámetros, la ruta se tiene que distinguir con una constante!
Route::get("/user/{nombre}/comment/{type}/{resourceName}/show", "UserCommentsController@showComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/save", "UserCommentsController@saveComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/delete/{id}", "UserCommentsController@deleteComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/update/{id}", "UserCommentsController@updateComment");
Route::get("/user/{nombre}/comment/{type}/{resourceName}/response/{id}", "UserCommentsController@responseComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/saveResponse/{id}", "UserCommentsController@saveResponseComment");
Route::get("/user/{nombre}/comment/{type}/{resourceName}/edit/{id}", "UserCommentsController@editComment");
Route::get("/user/{nombre}/comment/{id}/like", "UserCommentsController@like");
Route::get("/user/{nombre}/comment/{id}/dislike", "UserCommentsController@dislike");
Route::get("/user/{nombre}/{type}/{resourceName}", "UserController@show")->name("show");
Route::post("/user/{nombre}/rate/{type}/{resourceName}", "UserController@rating");
Route::get("/user/{nombre}/history", "UserController@history");
Route::post("/user/{nombre}/follow/{type}/{resourceName}", "UserController@follow");
Route::get("/user/{nombre}/followFeed", "UserController@allFollows");
Route::get("/user/{nombre}/follows", "UserController@followsView");
Route::get("/user/{nombre}/test", "UserController@test");
