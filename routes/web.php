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

$router->post('/users/login', ['uses'=>'UsersController@getToken']);
$router->post('/users', ['uses'=>'UsersController@createUser']);

$router->group(['middleware' => ['auth']], function () use ($router){
    $router->get('/users', ['uses'=>'UsersController@index']);

    $router->delete('users/{id}', ['uses' => 'UsersController@delete']);
});



$router->get('/', function () use ($router) {
    return $router->app->version();
});



