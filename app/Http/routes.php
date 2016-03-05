<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => 'web'], function () {

	Route::get('login', 'Auth\AuthController@showForm');
	Route::post('login', 'Auth\AuthController@authenticate');

});

Route::group(['middleware' => ['web', 'auth', 'subdomain']], function () {

    Route::get('/', 'PageController@dashboard');

	Route::group(['middleware' => 'admin'], function() {
	    // Environment
	    Route::resource('environments', 'EnvironmentController');
	    Route::resource('environments/{environment_id}/users', 'Auth\UserController');
		Route::get('/environment/{environment_id}/users/{user_id}', 'Auth\UserController@loginUsingId');
	});

	// Appointments
	Route::resource('appointments', 'AppointmentController');

	// Users
	Route::resource('users', 'Auth\UserController');

	Route::resource('company', 'CompanyController', ['only' => [
		'index', 'store', 'update'
	]]);

	Route::get('logout', 'Auth\Authcontroller@logout');

});
