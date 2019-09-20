<?php

namespace App\Transformers;

use App\Models\UserAddress;
use League\Fractal\TransformerAbstract;

/**
 * 地址数据转换层
 */
class UserAddressTransformer extends TransformerAbstract
{
  public function transform(UserAddress $userAddress)
  {
    return [
      'id'        => $userAddress->id,
      'user_id'   => $userAddress->user_id,
      'province'  => $userAddress->province,
      'city'      => $userAddress->city,
      'district'  => $userAddress->district,
      'address'   => $userAddress->address,
      'zip'       => $userAddress->zip,
      'contact_name'     => $userAddress->contact_name,
      'contact_phone'    => $userAddress->contact_phone,
      // 'last_used_at'     => (string)$userAddress->last_used_at,
      'created_at'  => (string) $userAddress->created_at,
      'updated_at'  => (string) $userAddress->updated_at,
    ];
  }
}