<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\Shop\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ads\Ad, App\Models\Shop\Product;
use Hash;

class CustomerController extends Controller
{
    function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:customers|max:255|email',
            'password' => 'required',
        ]);

        Customer::create([
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
    }

    function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        $customer = Customer::where("email", $email)->first();

        if (!$customer) {
            return response()->json([
                'msg' => '当前用户不存在~'
            ], 403);
        }

        if (!Hash::check($password, $customer->password)) {
            return response()->json([
                'msg' => '密码错误~'
            ], 403);
        }

        return $customer;
    }
}
