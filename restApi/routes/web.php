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

$router->get('/', function () use ($router) {
    return response()->json($router->app->version());
});

$router->get('path', function () use ($router) {
    return response()->json($router->app->path());
});

$router->get('fcr', function () use ($router) {
    // $pathSearch = $path;
    // $pathSearch = request()->segments(3);
    return ($router->app->filesCreated());
});
