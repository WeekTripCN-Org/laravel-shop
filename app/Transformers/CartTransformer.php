<?php

namespace App\Transformers;

use App\Models\CartItem;
use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * 分类数据转换层
 */
class CartTransformer extends TransformerAbstract
{
  protected $availableIncludes = ['user', 'productSku'];

  public function transform(CartItem $cartItem)
  {
    return [
      'id'              => $cartItem->id,
      'user_id'         => $cartItem->user_id,
      'product_sku_id'  => $cartItem->product_sku_id,
      'amount'          => $cartItem->amount,
      'created_at'      => (string)$cartItem->created_at,
      'updated_at'      => (string)$cartItem->updated_at,
    ];
  }

  public function includeUser(CartItem $cartItem)
  {
    return $this->item($cartItem->user, new UserTransformer());
  }

  public function includeProductSku(CartItem $cartItem)
  {
    return $this->item($cartItem->productSku, new ProductSkuTransformer());
  }
}