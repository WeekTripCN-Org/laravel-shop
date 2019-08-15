<?php
/**
 * 监听器，来执行发送邮件的动作
 */
namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// implements ShouldQueue 代表异步监听器
class SendOrderPaidMail implements ShouldQueue
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

        // 调用 notify 方法来发送通知
        $order->user->notify(new OrderPaidNotification($order));
    }
}
