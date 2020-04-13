<?php

use App\Http\Middleware\CorsMiddleware;

$router->group(['middleware' => [CorsMiddleware::class]], function () use ($router) {
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
    $router->group(['prefix' => 'website'], function () use ($router){
        $router->get('geteventlist','EventWebsiteController@getEventList');
        $router->post('register/{id}', 'EventWebsiteController@register');
    });
    
});