<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);

        // 订单已支付或者已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no'  => $order->no,
            'total_amount'  => $order->total_amount,
            'subject'       => '支付 Laravel Shop 的订单：' . $order->no,
        ]);
    }

    // 支付宝前端回调
    public function alipayReturn()
    {
        try {
            // 校验提交的参数是否合法
            app('alipay')->verify();
        } catch(\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }
        
        return view('pages.success', ['msg' => '付款成功']);
    }

    // 支付宝服务器回调
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // 订单流水号
        $order = Order::where('no', $data->out_trade_no)->first();
        if (!$order) {
            return 'fail';
        }
        if ($order->paid_at) {
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'           => Carbon::now(),   // 支付时间
            'payment_method'    => 'alipay',
            'payment_no'        => $data->trade_no, // 支付宝订单号
        ]);
        
        // 支付成功事件
        $this->afterPaid($order);
        
        return app('alipay')->success();
    }

    // 请求微信支付
    public function payByWechat(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 订单已支付或者已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // scan 方法为拉起微信扫码支付
        $wechatOrder = app('wechat_pay')->scan([
            'out_trade_no'      => $order->no,
            'total_fee'         => $order->total_amount * 100,                  // 金额单位是 分
            'body'              => '支付 Laravel Shop 的订单：' . $order->no,    // 订单描述
        ]);

        // 把要转换的字符串作为 QrCode 的构造函数参数
        $qrCode = new QrCode($wechatOrder->code_url);

        // 将生成的二维码数据以字符串的形式输出，并带上相应的响应类型
        return response($qrCode->writeString(), 200, ['Content-Type' => $qrCode->getContentType()]);
    }

    // 微信服务端回调
    public function wechatNotify()
    {
        $data = app('wechat_pay')->verify();

        $order = Order::where('no', $data->out_trade_no)->first();

        if (!$order) {
            return 'fail';
        }
        if ($order->paid_at) {
            return app('wechat_pay')->success();
        }

        $order->update([
            'paid_at'           => Carbon::now(),
            'payment_method'    => 'wechat',
            'payment_no'        => $data->transation_id
        ]);

        return app('wechat_pay')->success();
    }

    protected function afterPaid(Order $order) 
    {
        event(new OrderPaid($order));
    }

    // 微信退款回调通知
    public function wechatRefundNotify(Request $request)
    {
        // 给微信的失败响应
        $failXml = '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[FAIL]]></return_msg></xml>';
        $data = app('wechat_pay')->verify(null, true);

        // 没有找到对应的订单
        if (!$order = Order::where('no', $data['out_trade_no'])->first()) {
            return $failXml;
        }

        if ($data['refund_status'] === 'SUCCESS') {
            $order->update([
                'refund_status' => Order::REFUND_STATUS_SUCCESS,
            ]);
        } else {
            // 退款失败
            $extra = $order->extra;
            $extra['refund_failed_code'] = $data['refund_status'];
            $order->update([
                'refund_status' => Order::REFUND_STATUS_FAILED,
                'extra'         => $extra,
            ]);
        }
        return app('wechat_pay')->success();
    }
}
