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
//basic
Route::get('/', function () {
    return redirect()->route("home");
});
//Authentication routes (dealt with by Laravel built in authentication)
Auth::routes();
//navbar menu routes
Route::get('/home', 'HomeController@index')->name('home');
Route::resource("/admin/users", "AdminUsersController");
Route::get("/user/{nombre}/search", "UserController@search");
Route::get("/user/{nombre}/advancedSearch", "UserController@advancedSearch");
Route::post("/user/{nombre}/advancedSearch", "UserController@advancedSearch");
Route::get("/user/{nombre}", "UserController@index")->name("userIndex");
//Resource Main View Route
Route::get("/user/{nombre}/{type}/{resourceName}", "UserController@show")->name("show");
//download route
Route::post("/user/{nombre}/download/{type}/{resourceName}", "UserController@download");
//comments routes
Route::get("/user/{nombre}/comment/{type}/{resourceName}/show", "UserCommentsController@showComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/save", "UserCommentsController@saveComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/delete/{id}", "UserCommentsController@deleteComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/update/{id}", "UserCommentsController@updateComment");
Route::get("/user/{nombre}/comment/{type}/{resourceName}/response/{id}", "UserCommentsController@responseComment");
Route::post("/user/{nombre}/comment/{type}/{resourceName}/saveResponse/{id}", "UserCommentsController@saveResponseComment");
Route::get("/user/{nombre}/comment/{type}/{resourceName}/edit/{id}", "UserCommentsController@editComment");
//like/dislike routes
Route::get("/user/{nombre}/comment/{id}/like", "UserCommentsController@like");
Route::get("/user/{nombre}/comment/{id}/dislike", "UserCommentsController@dislike");
//rating route
Route::post("/user/{nombre}/rate/{type}/{resourceName}", "UserController@rating");
//history route
Route::get("/user/{nombre}/history", "UserController@history");
//routes for the follows views and updates
Route::post("/user/{nombre}/follow/{type}/{resourceName}", "UserController@follow");
Route::get("/user/{nombre}/followFeed", "UserController@allFollows");
Route::get("/user/{nombre}/follows", "UserController@followsView");
Route::get("/user/{nombre}/followsUpdates", "UserController@followsUpdates");
//test routes
Route::get("/user/{nombre}/test", "UserController@test");
Route::post("/user/{nombre}/testPOST", "UserController@testPOST")->name("upload");
