<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', function () use ($router) {
    return app()->version();
});

// route for creating access_token
$router->post('accessToken', 'AccessTokenController@createAccessToken');

$router->group(['prefix' => 'v1','middleware' => ['auth:api']], function () use ($router) {

	/***********************************
	 * Routes for resource users
	 **********************************/
	$router->group(['prefix' => 'users'], function () use ($router) {
	    $router->post('/', 'UserController@store');
	    $router->get('/', 'UserController@index');
	    $router->get('/{id}', 'UserController@show');
	    $router->put('/{id}', 'UserController@update');
	    $router->delete('/{id}', 'UserController@destroy');
	});

});
