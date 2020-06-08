<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use \Illuminate\Mail\Message;
use App\User;


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

$router->get('/test_mail', function (Request $request) {
	$user = User::where('api_token', $request->header('Api-Token'))->first();
	$mesage = ' we are excited to see you join in our team!';
	$code = 123456;

        Mail::send('emails.register', ['user' => $user->name, 'mesage'=>$mesage, 'code'=>$code], function ($m) use ($user) {
            $m->from('mail.focusapp@gmail.com', 'Focus Team');

            $m->to($user->email, $user->name)->subject('Welcome to focus!');
        });
	dd('Mail sent');
});



$router->post('/login', ['uses'=>'UsersController@getUser']);
$router->post('/register', ['uses'=>'UsersController@createUser']);


$router->group(['middleware' => ['auth']], function () use ($router){

$router->post('/verify', ['uses'=>'UsersController@verifyUser']);
$router->get('/resend', ['uses'=>'UsersController@resendMail']);


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



