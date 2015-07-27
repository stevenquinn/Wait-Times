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

Route::get('/', function () {
    return view('welcome');
});



// API
Route::group(['prefix' => 'api/v1'], function() 
{
	Route::get('ride-data', ['as' => 'ride-data', 'uses' => 'RideController@fetchRideData']);
	Route::get('park-hours', ['as' => 'park-hours', 'uses' => 'RideController@fetchParkHours']);
});
