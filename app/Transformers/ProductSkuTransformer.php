<?php

namespace App\Transformers;

use App\Models\ProductSku;
use League\Fractal\TransformerAbstract;

/**
 * 商品sku 数据转换层
 */
class ProductSkuTransformer extends TransformerAbstract
{
  public function transform(ProductSku $sku)
  {
    return [
      'id'          => $sku->id,
      'product_id'  => $sku->product_id,
      'title'       => $sku->title,
      'description' => $sku->description,
      'price'       => $sku->price,
      'stock'       => $sku->stock,
      'created_at'  => (string)$sku->created_at,
      'updated_at'  => (string)$sku->updated_at,
    ];
  }
}