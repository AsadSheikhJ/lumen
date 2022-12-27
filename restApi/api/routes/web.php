<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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


$router->get('/DeviceApi',['uses' => 'DataApiController@index']);

$router->group(['prefix' => '/DeviceApi/DataEntry'], function () use ($router) {

    $controller = 'DataApiController@DataEntry';

    $router->get('/{route:.*}/', $controller);
    $router->post('/{route:.*}/', $controller);
    $router->put('/{route:.*}/', $controller);
    $router->patch('/{route:.*}/', $controller);
    $router->delete('/{route:.*}/', $controller);

});

$router->group(['prefix' => '/DeviceApi/DataEntry2'], function () use ($router) {

    $controller = 'DataApiController@DataEntry2';

    $router->get('/{route:.*}/', $controller);
    $router->post('/{route:.*}/', $controller);
    $router->put('/{route:.*}/', $controller);
    $router->patch('/{route:.*}/', $controller);
    $router->delete('/{route:.*}/', $controller);

});

$router->get('/', function () use ($router) {
    return ($router->app->version());
});

$router->get('path', function () use ($router) {
    return response()->json($router->app->path());
});

$router->get('reader/fcr[/{path}]' ,function ($path = null) use ($router) {
    $pathSearch = $path;
    // $pathSearch = request()->segments(3);
    return ($router->app->filesCreated($pathSearch));
});
