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

Route::get('/', ['as' => 'home', 'uses' => 'ParkController@index']);
Route::get('park/{id}', ['as' => 'park', 'uses' => 'ParkController@show']);
Route::get('ride/{id}', ['as' => 'ride', 'uses' => 'RideController@show']);


// API
Route::group(['prefix' => 'api/v1'], function() 
{
	Route::get('ride-data', ['as' => 'ride-data', 'uses' => 'RideController@fetchRideData']);
	Route::get('park-hours', ['as' => 'park-hours', 'uses' => 'RideController@fetchParkHours']);
});
