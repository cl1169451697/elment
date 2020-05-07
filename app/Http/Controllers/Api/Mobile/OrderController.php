<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Shop\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB;
use App\Models\Shop\Order, App\Models\Shop\Cart, App\Models\Shop\Address, App\Models\Shop\Product, App\Models\Shop\OrderProduct, App\Models\Shop\OrderAddress;
use EasyWeChat, Carbon;
use EasyWeChat\Payment\Order as WechatOrder;

class OrderController extends Controller
{
    /**
     * 订单列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {
            $query->where('customer_id', auth('api')->user()->id);

            switch ($request->status) {
                case '':
                    break;
                case '1':
                    $query->where('status', 1);
                    break;
                case '2':
                    $query->whereIn('status', [2, 3, 4]);
                    break;
            }
        };

        $order_status = config('admin.order_status');
        $orders = Order::where($where)->with('order_products.product', 'customer', 'address')
            ->orderBy('created_at', 'desc')->get();

        return compact('orders', 'order_status');
    }

    /**
     * 显示
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $order = Order::with('address', 'express', 'customer', 'order_products.product')->find($id);
        return compact('order');
    }


    /**
     * 下单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    function checkout(Request $request)
    {

        $carts = Cart::where('customer_id', auth('api')->user()->id)->get();
        $count = Cart::count_cart($carts);

        //如果购物车中没有商品,跳回购物车页面
        if ($carts->isEmpty()) {
            return ['status' => 0];
        }

        $status = 1;
        $address = Address::find(auth('api')->user()->address_id);
        return compact('carts', 'count', 'address', 'status');
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    function destroy($id)
    {
        //查出对应的商品,并将库存还回去
        $order = Order::with('order_products')->find($id);
        foreach ($order->order_products as $order_product) {
            $product = Product::find($order_product->product_id);
            //如果不是无限库存
            if ($product->stock != '-1') {
                Product::where('id', $order_product->product_id)->increment('stock', $order_product->num);
            }
        }

        //删除订单商品
        OrderProduct::where('order_id', $id)->delete();
        //删除订单地址
        OrderAddress::where('order_id', $id)->delete();
        Order::destroy($id);
    }

    /**
     * 保存
     * @param Request $request
     * @return array
     */
    function store(Request $request)
    {
        $carts = Cart::with('product')->where('customer_id', auth('api')->user()->id)->get();

        //防止用户使用微信的后退按钮，重新提交订单，导致出现没有数据的订单
        if ($carts->isEmpty()) {
            return ['status' => 0, 'info' => ''];
        }

        $count = Cart::count_cart();
        $total_price = $count['total_price'];

        DB::beginTransaction();
        try {
            //生成订单
            $order = Order::create([
                'customer_id' => auth('api')->user()->id,
                'total_price' => $total_price,
                'status' => 1
            ]);

            //订单地址
            $address = Address::find($request->address_id);
            $order->address()->create([
                'province' => $address['province'],
                'city' => $address['city'],
                'area' => $address['area'],
                'detail' => $address['detail'],
                'name' => $address['name'],
                'tel' => $address['tel'],
            ]);

            $carts = Cart::with('product')->where('customer_id', auth('api')->user()->id)->get();
            foreach ($carts as $cart) {
                //判断库存是否足够
                if ($cart->product->stock != '-1' and $cart->product->stock - $cart->num < 0) {
                    throw new \Exception('商品' . $cart->product->name . ", 目前仅剩下" . $cart->product->stock . " 件. \n请返回购物车, 修改订单后再下单!");
                }

                //削减库存数量
                if ($cart->product->stock != '-1') {
                    $cart->product->decrement('stock', $cart->num);
                }

                //插入订单商品表
                $order->order_products()->create([
                    'product_id' => $cart->product_id,
                    'num' => $cart->num
                ]);
            }

            //清空购物车
            Cart::with('product')->where('customer_id', auth('api')->user()->id)->delete();

        } catch (\Exception $e) {
            //echo $e->getMessage();
            DB::rollback();
            return ['status' => 0, 'info' => $e->getMessage()];
        }
        DB::commit();
        return ['status' => 1, 'order_id' => $order->id];
    }

    /**
     * 微信支付
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function pay($id)
    {
        /**
         * 第 1 步：查询订单并计算金额
         */
        $order = Order::with('address')->find($id);

        //计算总价格, 以分为单位, 所以: *100
        $total_fee = ($order->total_price + $order->express_money) * 100;

        /**
         * 第 2 步：创建订单
         */
        $attributes = [
            'openid' => session('wechat.customer.openid'),
            'body' => '订单号:' . $order->id,
            'detail' => '长乐小卖部',
            'out_trade_no' => $order->id,
            'total_fee' => $total_fee,
            'trade_type' => 'JSAPI',
            'notify_url' => 'http://' . env('WECHAT_DOMAIN') . '/order/notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
        ];

        $wechat_order = new WechatOrder($attributes);

        /**
         * 第 3 步：统一下单
         */
        $payment = EasyWeChat::payment();

        $result = $payment->prepare($wechat_order);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepayId = $result->prepay_id;
            $json = $payment->configForPayment($prepayId);
            return compact('order', 'json');
        } else {
            return $result;
        }
    }

    /**
     * 微信支付回调方法，修改订单状态
     * @return mixed
     */
    function notify()
    {
        $payment = EasyWeChat::payment();
        $response = $payment->handleNotify(function ($notify, $successful) {

            $order = Order::find($notify->out_trade_no);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            // 如果订单存在，检查订单是否已经更新过支付状态
            if ($order->status >= 2) { // 状态 >= 2 代表已经支付
                return true; // 已经支付成功了就不再更新了
            }

            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->pay_time = Carbon\Carbon::now(); // 更新支付时间为当前时间
                $order->status = 2;
            } else {
                $order->status = 1;
            }

            $order->save(); // 保存订单
            return true; // 返回处理完成
        });

        return $response;
    }
}