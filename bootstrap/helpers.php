<?php

  function route_class() 
  {
    return str_replace('.', '-', Route::currentRouteName());
  }

  function proxy_url($routeName, $parameters = [])
  {
    // 开发环境，并且配置了 PROXY_URL
    if (app()->environment('local') && $url = config('app.proxy_url')) {
      // route() 函数第三个参数代表是否绝对路径
      return $url . route($routeName, $parameters, false);
    }

    return route($routeName, $parameters);
  }