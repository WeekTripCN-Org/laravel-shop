<?php
/**
 * Dingo/Api + jwt-auth
 * 
 * put 替换某个资源，需提供完整的资源信息
 * patch 部分修改资源，提供部分资源信息
 */
use Illuminate\Http\Request;

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware'=> 'serializer:array'
], function($api) {
    $api->group([
        'middleware' => 'api.throttle', // 调用频率限制的中间件
        'limit'      => config('api.rate_limits.sign.limit'),
        'expires'    => config('api.rate_limits.sign.expires'),
    ], function($api) {
        // 游客可以访问的接口 {{{
        // 分类列表
        $api->get('categories', 'CategoriesController@index')->name('api.categories.index');
        // 子类数据 post@parent_id
        $api->post('categories', 'CategoriesController@show')->name('api.categories.show');
        // }}}

        

        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {
            // 当前登录用户信息
            $api->get('user', 'UsersController@me')->name('api.user.show');
            // 上传图片资源
            $api->post('images', 'ImagesController@store')->name('api.images.store');
            // 编辑登录用户信息
            $api->patch('user', 'UsersController@update')->name('api.user.update');

        });

        // 短信验证码
        $api->post('verificationCodes', 'verificationCodesController@store')->name('api.verificationCodes.store');
        // 用户注册
        $api->post('users', 'UsersController@store')->name('api.users.store');
        // 图片验证码
        $api->post('captchas', 'CaptchasController@store')->name('api.captchas.store');
        // 第三方登录
        $api->post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')->name('api.socials.authorizations.store');
        // 用户登录
        $api->post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');
        // 刷新token
        $api->put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');
        // 删除token
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')->name('api.authorizations.destroy');
    });
    
});

