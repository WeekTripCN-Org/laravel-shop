<?php
/**
 * Dingo/Api + jwt-auth
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Api\Controllers\V1'], function($api) {
    $api->post('login', 'AuthController@login');
    $api->post('logout', 'AuthController@logout');
    $api->post('refresh', 'AuthController@refresh');
    $api->post('me', 'AuthController@me');
    $api->post('register', 'AuthController@register');

    $api->post('getProductsByCategoryId', 'ProductsController@getProductsByCategoryId');
    
});

