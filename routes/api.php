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
    return $router->app->version();
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthenticationController@logIn');
});
$router->group(['prefix' => 'event'], function () use ($router) {
    $router->get('get/list', 'EventController@getEventList');
    $router->get('get/{id}', 'EventController@getEvent');
    $router->post('add', 'EventController@addEvent');
    $router->patch('edit/{id}', 'EventController@editEvent');
    $router->patch('delete', 'EventController@deleteEvent');
});