<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * 事件监听器，来实现众筹进度的更新
 */
class UpdateCrowdfundingProductProgress implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();

        // 订单类型是否为众筹类型
        if ($order->type !== Order::TYPE_CROWDFUNDING) {
            return;
        }

        $crowdfunding = $order->items[0]->product->crowdfunding;

        $data = Order::query()
            // 查出订单类型为众筹订单
            ->where('type', Order::TYPE_CROWDFUNDING)
            // 并且是已支付的
            ->whereNotNull('paid_at')
            ->whereHas('items', function($query) use ($crowdfunding) {
                // 并且包含了本商品
                $query->where('product_id', $crowdfunding->product_id);
            })
            ->first([
                // Laravel 在构建 SQL 的时候如果遇到 DB::raw() 就会把 DB::raw() 的参数原样拼接到 SQL 里
                // 取出订单总金额
                \DB::raw('sum(total_amount) as total_amount'),
                // 取出去重的支持用户数
                \DB::raw('count(distinct(user_id)) as user_count'),
            ]);
        $crowdfunding->update([
            'total_amount'  => $data->total_amount,
            'user_count'    => $data->user_count,
        ]);
    }
}
