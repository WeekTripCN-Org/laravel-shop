<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;

class OrderService
{
  public function store(User $user, UserAddress $address, $remark, $items)
  {
    // 开启一个数据库事务
    $order = \DB::transaction(function () use ($user, $address, $remark, $items) {
      // 更新此地址的最后使用时间
      $address->update(['last_used_at' => Carbon::now()]);
      // 创建一个订单
      $order = new Order([
        'address' => [
            'address'       => $address->full_address,
            'zip'           => $address->zip,
            'contact_name'  => $address->contact_name,
            'contact_phone' => $address->contact_phone,
        ],
        'remark'            => $remark,
        'total_amount'      => 0,
      ]);
      // 订单关联到当前用户
      $order->user()->associate($user);
      // 写入数据库
      $order->save();

      $totalAmount = 0;
      // 遍历提交的 SKU
      foreach ($items as $data) {
        $sku = ProductSku::find($data['sku_id']);
        // 创建一个 OrderItem 并直接与当前订单关联
        $item = $order->items()->make([
            'amount'    => $data['amount'],
            'price'     => $sku->price,
        ]);
        $item->product()->associate($sku->product_id);
        $item->productSku()->associate($sku);
        $item->save();
        $totalAmount += $sku->price * $data['amount'];

        if ($sku->decreaseStock($data['amount']) <= 0) {
            throw new InvalidRequestException('该商品库存不足');
        }
      }
      // 更新订单总金额
      $order->update(['total_amount' => $totalAmount]);

      // 将下单的商品从购物车中移除
      $skuIds = collect($items)->pluck('sku_id')->all();
      
      // CartService 的调用方式改为了通过 app() 函数创建，
      //因为这个 store() 方法是我们手动调用的，无法通过 Laravel 容器的自动解析来注入
      app(CartService::class)->remove($skuIds);

      return $order;
    });

    // 触发关闭订单任务
    // 需要执行 php artisan queue:work
    dispatch(new CloseOrder($order, config('app.order_ttl')));

    return $order;
  }
}