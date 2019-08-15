<?php

// Route::get('/', 'PagesController@root')->name('root')->middleware('verified');
Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');

Auth::routes(['verify' => true]);

// auth 中间件代表需要登录，verified 中间件代表需要经过邮箱验证
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');

    Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');

    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');

    Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');

    Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');

    Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');

    // 收藏和取消收藏商品 {{{
    Route::post('products/{product}/favorite', 'ProductsController@favor')->name('products.favor');
    Route::delete('products/{product}/favorite', 'ProductsController@disfavor')->name('products.disfavor');
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
    // }}}

    // 添加购物车 {{{
    Route::post('cart', 'CartController@add')->name('cart.add');
    // }}}

    // 查看购物车
    Route::get('cart', 'CartController@index')->name('cart.index');
    // 删除
    Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');

    Route::post('orders', 'OrdersController@store')->name('orders.store');
});

// 和我的收藏冲突了，移到最下面
Route::get('products/{product}', 'ProductsController@show')->name('products.show');