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
Route::post("/user/{nombre}/comment/{type}/{resourceName}/save", "UserCommentsController@saveComment");
Route::delete("/user/{nombre}/comment/{type}/{resourceName}/delete", "UserCommentsController@deleteComment");
Route::put("/user/{nombre}/comment/{type}/{resourceName}/update", "UserCommentsController@updateComment");
Route::get("/user/{nombre}/comment/{type}/{resourceName}/edit", "UserCommentsController@editComment");
Route::get("/user/{nombre}/{type}/{resourceName}", "UserController@show")->name("show");
Route::get("/user/{nombre}/history", "UserController@history");
Route::get("/user/{nombre}/email", "UserController@email");
