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

$router->post('/login', ['uses'=>'UsersController@getToken']);
$router->post('/register', ['uses'=>'UsersController@createUser']);

$router->group(['middleware' => ['auth']], function () use ($router){

    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', ['uses' => 'UsersController@index']);
        $router->delete('/', ['uses' => 'UsersController@delete']);
    });

    $router->group(['prefix' => 'subject'], function () use ($router) {
        $router->post('/', ['uses' => 'SubjectsController@add']);
        $router->get('/', ['uses' => 'SubjectsController@list']);
        $router->put('/{id}', ['uses' => 'SubjectsController@update']);
        $router->delete('/{id}', ['uses' => 'SubjectsController@delete']);
    });

    $router->group(['prefix' => 'topic'], function () use ($router) {
        $router->post('/{id}', ['uses' => 'TopicsController@add']);
        $router->get('/{id}', ['uses' => 'TopicsController@list']);});
        $router->put('/{id}', ['uses' => 'TopicsController@update']);
        $router->delete('/{id}', ['uses' => 'TopicsController@delete']);
    });

$router->get('/', function () use ($router) {
    return $router->app->version();
});



