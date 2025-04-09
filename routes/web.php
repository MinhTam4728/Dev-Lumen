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

$router->group(['middleware' => 'cors'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/logout', ['middleware' => 'auth', 'uses' => 'AuthController@logout']);

    $router->group(['middleware' => 'auth:api'], function () use ($router) {
        // Admin routes
        $router->group(['prefix' => 'admin'], function () use ($router) {
            $router->get('/customers', ['middleware' => 'role:0', 'uses' => 'CustomerController@index']);
            $router->get('/orders', ['middleware' => 'role:0', 'uses' => 'CustomerController@allOrders']);
            $router->post('/customers', ['middleware' => 'role:0', 'uses' => 'CustomerController@store']);
            $router->put('/customers/{id}', ['middleware' => 'role:0', 'uses' => 'CustomerController@update']);
            $router->put('/change-password', ['middleware' => 'role:0', 'uses' => 'CustomerController@changePassword']);
            $router->delete('/customers/{id}', ['middleware' => 'role:0', 'uses' => 'CustomerController@destroy']);
        });

        // Customer routes
        $router->group(['prefix' => 'customer'], function () use ($router) {
            $router->get('/info', ['middleware' => 'role:1', 'uses' => 'CustomerController@show']);
            $router->put('/', ['middleware' => 'role:1', 'uses' => 'CustomerController@updateProfile']);
            $router->put('/change-password', ['middleware' => 'role:1', 'uses' => 'CustomerController@changePasswordCustomer']);
            $router->get('/orders', ['middleware' => 'role:1', 'uses' => 'CustomerController@orders']);
        });
    });
});