<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

/**
 * 商品数据转换层
 */
class ProductTransformer extends TransformerAbstract
{
  // 额外资源数据
  protected $availableIncludes = ['skus', 'category', 'properties'];

  public function transform(Product $product)
  {
    return [
      'id'          => $product->id,
      'type'        => $product->type,
      'category_id' => $product->category_id,
      'title'       => $product->title,
      'long_title'  => $product->long_title,
      'description' => $product->description,
      'image'       => $product->image,
      'rating'      => $product->rating,
      'sold_count'  => $product->sold_count,
      'on_sale'     => $product->on_sale,
      'review_count'=> $product->review_count,
      'price'       => $product->price,
      'created_at'  => (string)$product->created_at,
      'updated_at'  => (string)$product->updated_at,
    ];
  }

  // 返回商品对应的类目
  public function includeCategory(Product $product)
  {
    return $this->item($product->category, new CategoryTransformer());
  }

  // 返回商品的 skus 集合
  public function includeSkus(Product $product)
  {
    return $this->collection($product->skus, new ProductSkuTransformer());
  }
}