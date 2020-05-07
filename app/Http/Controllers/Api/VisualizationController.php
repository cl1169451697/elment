<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use DB, Cache;
use App\Models\Shop\Order, App\Models\Shop\OrderProduct, App\Models\Crm\Student, App\Models\Shop\Customer;

class VisualizationController extends Controller
{
    //本周起止时间unix时间戳
    private $week_start;
    private $week_end;

    //本月起止时间unix时间戳
    private $month_start;
    private $month_end;

    function __construct()
    {
        $this->week_start = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
        $this->week_end = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));

        $this->month_start = mktime(0, 0, 0, date("m"), 1, date("Y"));
        $this->month_end = mktime(23, 59, 59, date("m"), date("t"), date("Y"));
    }

    /**
     * 本周订单数
     * @return array
     */
    function sales_count()
    {
        return Cache::remember('xApi_visualization_sales_count', 60, function () {
            $count = [];
            for ($i = 0; $i < 7; $i++) {
                $start = date('Y-m-d H:i:s', strtotime("+" . $i . " day", $this->week_start));
                $end = date('Y-m-d H:i:s', strtotime("+" . ($i + 1) . " day", $this->week_start));

                //待支付
                $count['create'][] = Order::whereBetween('created_at', [$start, $end])->where('status', 1)->count();

                $count['pay'][] = Order::whereBetween('pay_time', [$start, $end])->where('status', 2)->count();

                $count['picking'][] = Order::whereBetween('picking_time', [$start, $end])->where('status', 3)->count();

                $count['shipping'][] = Order::whereBetween('shipping_time', [$start, $end])->where('status', 4)->count();

                $count['finish'][] = Order::whereBetween('finish_time', [$start, $end])->where('status', 5)->count();
            }

            $data = [
                'week_start' => date("Y年m月d日", $this->week_start),
                'week_end' => date("Y年m月d日", $this->week_end),
                'count' => $count,
            ];
            return $data;
        });

    }

    /**
     * 本周销售额
     * @return array
     */
    function sales_amount()
    {
        return Cache::remember('xApi_visualization_sales_amount', 60, function () {
            $amount = [];
            for ($i = 0; $i < 7; $i++) {
                $start = date('Y-m-d H:i:s', strtotime("+" . $i . " day", $this->week_start));
                $end = date('Y-m-d H:i:s', strtotime("+" . ($i + 1) . " day", $this->week_start));
                $amount['create'][] = Order::whereBetween('created_at', [$start, $end])->where('status', 1)->sum('total_price');
                $amount['pay'][] = Order::whereBetween('pay_time', [$start, $end])->where('status', '>', 1)->sum('total_price');
            }

            $data = [
                'week_start' => date("Y年m月d日", $this->week_start),
                'week_end' => date("Y年m月d日", $this->week_end),
                'amount' => $amount,
            ];
            return $data;
        });
    }


    /**
     * 本月热门销量
     * @return mixed
     */
    function top()
    {
        return Cache::remember('xApi_visualization_top', 60, function () {
//            DB::enableQueryLog();
            $start = date("Y-m-d H:i:s", $this->month_start);
            $end = date("Y-m-d H:i:s", $this->month_end);

            //本月订单的id
            $order = Order::whereBetween('created_at', [$start, $end])->pluck('id');

            //对应热门商品,前10名. 语句较复杂,请自己return sql出来看
            $products = OrderProduct::with('product')
                ->select('product_id', DB::raw('sum(num) as sum_num'))
                ->whereIn('order_id', $order)
                ->groupBy('product_id')
                ->orderBy(DB::raw('sum(num)'), 'desc')
                ->take(5)
                ->get();


            // return DB::getQueryLog();

            $data = [
                'month_start' => date("Y年m月d日", $this->month_start),
                'month_end' => date("Y年m月d日", $this->month_end),
                'products' => $products,
            ];
            return $data;
        });

    }

    /**
     * 本月每个地区的销量和金额
     */
    function sales_area()
    {
//        return Cache::remember('xApi_visualization_sales_area', 60, function () {

        $start = date("Y-m-d H:i:s", $this->month_start);
        $end = date("Y-m-d H:i:s", $this->month_end);

        //本月订单的id
        DB::enableQueryLog();
        $orders = Order::with('address')
            ->select('address_id', DB::raw('sum(total_price) as sum_num'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('address_id')
            ->get();
        return DB::getQueryLog();

        $data = [
            'month_start' => date("Y年m月d日", $this->month_start),
            'month_end' => date("Y年m月d日", $this->month_end),
            'orders' => $orders,
        ];
//            return $data;
//        });
    }

    /**
     * 本年度,学生报名数量统计
     * @return array
     */
    function students_count()
    {
        $year = date("Y", time());
        $num = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = strlen($i) == 1 ? '0' . $i : $i;
            $like = $year . '_' . $month . '%';
            $num[] = Student::where('created_at', 'like', $like)->count();
        }

        $data = [
            'this_year' => $year,
            'num' => $num
        ];
        return $data;
    }


    /**
     * 学生来源
     * @return array
     */
    function find_us()
    {
        $data = [
            ['name' => '长乐未央官网', 'value' => Student::where('find_us', '1')->count()],
            ['name' => '朋友介绍', 'value' => Student::where('find_us', '2')->count()],
            ['name' => 'QQ群', 'value' => Student::where('find_us', '3')->count()],
            ['name' => '其他', 'value' => Student::where('find_us', '4')->count()],
        ];
        return $data;
    }

    /**
     * 付款方式
     * @return array
     */
    function pay_type()
    {
        $data = [
            ['name' => '现金/支付宝', 'value' => Student::where('pay_type', '1')->count()],
            ['name' => '刷卡', 'value' => Student::where('pay_type', '2')->count()],
            ['name' => '百度钱包', 'value' => Student::where('pay_type', '3')->count()],
            ['name' => '阳光钱包', 'value' => Student::where('pay_type', '4')->count()],
        ];
        return $data;
    }

    /**
     * 平均工资
     * @return array
     */
    function avg_salary()
    {
        $student = Student::where("is_finished", true)->select('name', 'salary', 'created_at')->get();
        $data = [];
        foreach ($student as $v) {
            $date = $v->created_at->format("Y-m-d");
            $data[0][] = [$date, $v->salary, 1, $v->name];
        }
        return $data;
    }

    /**
     * 性别统计
     * @return \Illuminate\Support\Collection
     */
    function sex_count()
    {
        $male = Customer::where('sex', '1')->count();
        $female = Customer::where('sex', '2')->count();
        return collect(compact('male', 'female'));
    }

    function customer_province()
    {
        $count = Customer::select(DB::raw('province as name, count(*) as value'))->groupBy('province')->get();
        return $count;
    }
}
