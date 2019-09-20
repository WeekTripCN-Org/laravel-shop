<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ProductSku;
use App\Transformers\CartTransformer;
use App\Http\Requests\Api\CartRequest;
use App\Models\CartItem;

class CartsController extends Controller
{
    public function index()
    {
        $carts = $this->user()->cartItems()->with(['productSku.product'])->get();
        return $this->response->collection($carts, new CartTransformer());
    }

    public function store(CartRequest $cartRequest, CartItem $cartItem)
    {
        $skuId = $cartRequest->sku_id;
        $amount = $cartRequest->amount;

        $user = $this->user();
        // 从数据库中查询该商品是否已经在购物车中
        if ($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            // 如果存在则直接叠加商品数量
            $item->update([
                'amount'    => $item->amount + $amount,
            ]);
        } else {
            // 否则创建一个新的购物车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $this->response->created();
    }

    public function destroy($sku_id)
    {
        if (!is_array($sku_id)) {
            $sku_id = [$sku_id];
          }
        $this->user()->cartItems()->whereIn('product_sku_id', $sku_id)->delete();
        return $this->response->noContent();
    }
}
