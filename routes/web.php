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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource("admin/users", "AdminUsersController");
//se accede desde el usuario
//Route::get("/mangapark/{mangaName}/info", "MangaparkScrapperController@info");
//Route::resource("user/{nombre}", "UserController");
//voy a hacerlo desde el formulario
Route::get("/user/{nombre}/search", "UserController@search");
Route::post("/user/{nombre}/download", "UserController@download");
Route::get("/user/{nombre}", "UserController@index");
Route::resource("/user/{nombre}/comment", "UserCommentsController");
Route::get("/user/{nombre}/manga/{mangaName}", "UserController@showManga");
Route::get("/user/{nombre}/history", "UserController@history");
