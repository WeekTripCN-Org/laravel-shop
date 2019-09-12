<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * 用户数据转换层
 */
class UserTransformer extends TransformerAbstract
{
  public function transform(User $user)
  {
    return [
      'id'    => $user->id,
      'name'  => $user->name,
      'email' => $user->email,
      'avatar'=> $user->avatar,
      'bound_phone' => $user->phone ? true : false,                                     // 是否绑定手机
      'bound_wechat'=> ($user->weixin_unionid && $user->weixin_openid) ? true : false,  // 是否绑定微信
      'created_at'  => (string) $user->created_at,
      'updated_at'  => (string) $user->updated_at,
    ];
  }
}