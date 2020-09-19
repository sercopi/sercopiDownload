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
Route::resource("/user/{nombre}/comment", "UserCommentsController");
Route::get("/user/{nombre}/{type}/{resourceName}", "UserController@show");
Route::get("/user/{nombre}/history", "UserController@history");
