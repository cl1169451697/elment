<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop\Cart;


class CartController extends Controller
{
    /**
     * 购物车列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(Request $request)
    {
        $carts = Cart::where('customer_id', auth('api')->user()->id)->get();
        $count = Cart::count_cart($carts);
        return compact('carts', 'count');
    }

    /**
     * 添加商品到购物车
     * @param Request $request
     * @return array|void
     */
    function store(Request $request)
    {
        //判断购物车是否有当前商品,如果有,那么 num +1

        $product_id = $request->product_id;
        $cart = Cart::where('product_id', $product_id)->where('customer_id', auth('api')->user()->id)->first();


        if ($cart) {
            Cart::where('id', $cart->id)->increment('num');
            return;
        }

        //否则购物车表,创建新数据
        Cart::create([
            'product_id' => $request->product_id,
            'customer_id' => auth('api')->user()->id,
        ]);

    }

    /**
     * 修改购物车商品数量
     * @param Request $request
     * @return array
     */
    function change_num(Request $request)
    {
        $cart = Cart::find($request->cart_id);
        if ($request->type == 'inc') {
            $cart->increment('num');
        } else {
            $cart->decrement('num');
        }

        $carts = Cart::where('customer_id', auth('api')->user()->id)->get();
        $count = Cart::count_cart($carts);
        return compact('carts', 'count');
    }

    /**
     * 删除
     * @param Request $request
     * @return array
     */
    function destroy(Request $request)
    {
        $cart_id = $request->cart_id;
        Cart::destroy($cart_id);

        $carts = Cart::where('customer_id', auth('api')->user()->id)->get();
        $count = Cart::count_cart($carts);
        return compact('carts', 'count');
    }
}