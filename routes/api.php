<?php
/**
 * Dingo/Api + jwt-auth
 */
use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
], function($api) {
    // 短信验证码
    $api->post('verificationCodes', 'verificationCodesController@store')->name('api.verificationCodes.store');
    
    // $api->post('login', 'AuthController@login');
    // $api->post('logout', 'AuthController@logout');
    // $api->post('refresh', 'AuthController@refresh');
    // $api->post('me', 'AuthController@me');
    // $api->post('register', 'AuthController@register');

    // $api->post('getProductsByCategoryId', 'ProductsController@getProductsByCategoryId');
    
});

