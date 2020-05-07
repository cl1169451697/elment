<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop\Customer;

class CustomersController extends Controller
{
    /**
     * 会员列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {	
        //多条件查找
        $where = function ($query) use ($request) {
            if ($request->has('nickname') and $request->nickname != '') {
                $nickname = "%" . $request->nickname . "%";
                $query->where('nickname', 'like', $nickname);
            }

            if ($request->has('openid') and $request->openid != '') {
                $openid = "%" . $request->openid . "%";
                $query->where('openid', 'like', $openid);
            }

            if ($request->has('sex') and $request->sex != '') {
                $query->where('sex', $request->sex);
            }

            if ($request->has('created_at') and $request->created_at != '') {
                $time = explode(",", $request->input('created_at'));
                foreach ($time as $k => $v) {
                    $time["$k"] = $v;
                }
                $query->whereBetween('created_at', $time);
            }
        };

        $customers = Customer::where($where)->paginate(config('admin.page_size'));
        return ['success' => true, 'msg' => '查询成功', 'data' => compact('customers')];
    }

}
