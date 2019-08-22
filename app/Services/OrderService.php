<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InternalException;

class OrderService
{
  public function store(User $user, UserAddress $address, $remark, $items, CouponCode $coupon = null)
  {
    // 如果传了优惠券，则先检查是否可用
    if ($coupon) {
      $coupon->checkAvailable($user);
    }
    // 开启一个数据库事务
    $order = \DB::transaction(function () use ($user, $address, $remark, $items, $coupon) {
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
        'type'              => Order::TYPE_NORMAL,
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

      if($coupon) {
        // 总金额已经计算出来了，检查是否符合优惠券规则
        $coupon->checkAvailable($user, $totalAmount);
        // 把订单金额修改为优惠后的金额
        $totalAmount = $coupon->getAdjustedPrice($totalAmount);
        // 将订单与优惠券关联
        $order->couponCode()->associate($coupon);
        // 增加优惠券的用量，需判断返回值
        if ($coupon->changeUsed() <= 0 ) {
          throw new CouponCodeUnavailableException('该优惠券已被对完');
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

  // 实现众筹商品下单逻辑
  public function crowdfunding(User $user, UserAddress $address, ProductSku $sku, $amount)
  {
    // 开启事务
    $order = \DB::transaction(function () use ($amount, $sku, $user, $address) {
      // 更新地址最后使用时间
      $address->update(['last_used_at' => Carbon::now()]);
      // 创建一个订单
      $order = new Order([
        'address' => [
          'address'     => $address->full_address,
          'zip'         => $address->zip,
          'contact_name'  => $address->contact_name,
          'contact_phone' => $address->contact_phone,
        ],
        'remark'        => '',
        'total_amount'  => $sku->price * $amount,
        'type'          => Order::TYPE_CROWDFUNDING,
      ]);

      // 订单关联当前用户
      $order->user()->associate($user);

      $order->save();
      // 创建一个新的订单项并于 SKU 关联
      $item = $order->items()->make([
        'amount'  => $amount,
        'price'   => $sku->price,
      ]);
      $item->product()->associate($sku->product_id);
      $item->productSku()->associate($sku);
      $item->save();

      // 扣减对应的 SKU 库存
      if ($sku->decreaseStock($amount) <= 0) {
        throw new InvalidRequestException('该商品库存不足');
      }

      return $order;
    });

    // 众筹结束时间减去当前时间得到剩余秒数
    $crowdfundingTtl = $sku->product->crowdfunding->end_at->getTimestamp() - time();

    // 剩余秒数与默认订单关闭时间取较小值作为关闭时间
    dispatch(new CloseOrder($order, min(config('app.order_ttl'), $crowdfundingTtl)));

    return $order;
  }

  // 退款逻辑
  public function refundOrder(Order $order)
  {
    // 判断该订单的支付方式
    switch ($order->payment_method) {
      case 'wechat':
        //生成退款订单号
        $refundNo = Order::getAvailableRefundNo();
        app('wechat_pay')->refund([
          'out_trade_no'  => $order->no,
          'total_fee'     => $order->total_amount * 100,
          'refund_fee'    => $order->total_amount * 100,
          'out_refund_no' => $refundNo,
          'notify_url'    => proxy_url('payment.wechat.refund_notify')
        ]);
        // 将订单状态改成退款中
        $order->update([
            'refund_no' => $refundNo,
            'refund_status' => Order::REFUND_STATUS_PROCESSING,
        ]);
        break;
      case 'alipay':
        $refundNo = Order::getAvailableRefundNo();
        // 调用支付宝的支付实例的 refund 方法
        $ret = app('alipay')->refund([
            'out_trade_no'  => $order->no,
            'refund_amount' => $order->total_amount,
            'out_request_no'=> $refundNo,
        ]);

        // 如果返回值里有 sub_code 字段说明退款失败
        if ($ret->sub_code) {
            // 将退款失败的保存 extra 字段
            $extra = $order->extra;
            $extra['refund_failed_code'] = $ret->sub_code;
            // 将订单标记为退款失败
            $order->update([
                'refund_no' => $refundNo,
                'refund_status' => Order::REFUND_STATUS_FAILED,
                'extra'     => $extra,
            ]);
        } else {
            $order->update([
                'refund_no' => $refundNo,
                'refund_status' => Order::REFUND_STATUS_SUCCESS
            ]);
        }
        break;
      default:
        throw new InternalException('未知订单支付方式：' . $order->payment_method);
        break;
    }
  }

  // 秒杀逻辑
  public function seckill(User $user, UserAddress $address, ProductSku $sku)
  {
    // 开启事务
    $order = \DB::transaction(function () use ($sku, $user, $address) {
      // 更新地址最后使用时间
      $address->update(['last_used_at' => Carbon::now()]);
      // 创建一个订单
      $order = new Order([
        'address' => [
          'address'     => $address->full_address,
          'zip'         => $address->zip,
          'contact_name'  => $address->contact_name,
          'contact_phone' => $address->contact_phone,
        ],
        'remark'        => '',
        'total_amount'  => $sku->price,
        'type'          => Order::TYPE_SECKILL,
      ]);

      // 订单关联当前用户
      $order->user()->associate($user);

      $order->save();
      // 创建一个新的订单项并于 SKU 关联
      $item = $order->items()->make([
        'amount'  => 1, // 秒杀商品只能一份
        'price'   => $sku->price,
      ]);
      $item->product()->associate($sku->product_id);
      $item->productSku()->associate($sku);
      $item->save();

      // 扣减对应的 SKU 库存
      if ($sku->decreaseStock(1) <= 0) {
        throw new InvalidRequestException('该商品库存不足');
      }

      return $order;
    });

    // 秒杀订单的自动关闭时间与普通订单不同
    dispatch(new CloseOrder($order, config('app.seckill_order_ttl')));

    return $order;
  }
}