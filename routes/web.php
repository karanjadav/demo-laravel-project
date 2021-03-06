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
    return redirect()->route('home');
});

Auth::routes();
Route::get('login/{provider}', 'SocialController@redirect');
Route::get('login/{provider}/callback','SocialController@Callback');

Route::group(['middleware' => 'CheckAuthRole'],function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('user', UsersController::class);
    Route::get('user/delete/{user}', 'UsersController@destroy')->name('user.destroy');
    Route::resource('post', PostController::class);
    Route::get('post/delete/{post}', 'PostController@destroy')->name('post.destroy');
});
