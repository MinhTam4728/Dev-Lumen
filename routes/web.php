<?php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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
    return $router->app->version();
});

$router->post('/login', 'AuthController@login');
$router->post('/logout', ['middleware' => 'auth', 'uses' => 'AuthController@logout']);

// Add auth middleware to all routes
$router->group(['middleware' => 'auth:api'], function () use ($router) {
    // Admin routes
    $router->group(['prefix' => 'admin'], function () use ($router) {
        $router->get('/customers', [
            'middleware' => 'role:0',
            'uses' => 'CustomerController@index'
        ]);
        $router->post('/customers', [
            'middleware' => 'role:0',
            'uses' => 'CustomerController@store'
        ]);
        $router->put('/customers/{id}', [
            'middleware' => 'role:0',
            'uses' => 'CustomerController@update'
        ]);
        $router->delete('/customers/{id}', [
            'middleware' => 'role:0',
            'uses' => 'CustomerController@destroy'
        ]);
    });

    // Customer routes
    $router->group(['prefix' => 'customer'], function () use ($router) {
        $router->get('/', [
            'middleware' => 'role:1',
            'uses' => 'CustomerController@show'
        ]);
        $router->put('/', [
            'middleware' => 'role:1',
            'uses' => 'CustomerController@updateProfile'
        ]);
        $router->get('/orders', [
            'middleware' => 'role:1',
            'uses' => 'CustomerController@orders'
        ]);
    });
});
