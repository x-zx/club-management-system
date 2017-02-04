<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


Route::get('/','HomeController@index');
Route::post('upload','UploadController@upload');
Route::get('/maps/{key}','MapController@show');
Route::post('/maps/{key}','MapController@save');
Route::controller('wechat','WechatController');

Route::group(['middleware' => ['web']], function () {

	Route::controller('auth','AuthController');
	Route::controller('check','CheckController');
	Route::post('/users/{id}/invite', 'UserController@postInvite');
	Route::resource('users', 'UserController');
	Route::get('/clubs/{id}/checked', 'ClubController@getChecked');
	Route::resource('clubs', 'ClubController');
	Route::resource('rooms', 'RoomController');
	Route::resource('reservations', 'ReservationController');
	
	
});

Route::group(['middleware' => 'web'], function () {
    //Route::auth();
    //Route::get('/home', 'HomeController@index');
});
