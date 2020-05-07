<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Shop\Product, App\Models\Shop\Category;

class ProductController extends Controller
{
    function category()
    {
        $categories = Category::get_categories();
        return $categories;
    }

    /**
     * 商品列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(Request $request)
    {
        $where = function ($query) use ($request) {
            if ($request->has('id') and $request->id != '') {
                $id = $request->id;
                $product_ids = \DB::table('category_product')->where('category_id', $id)->pluck('product_id');

                $query->whereIn('id', $product_ids);
            }

            if ($request->has('searchword')) {
                if ($request->has('searchword') and $request->searchword != '') {
                    $search = "%" . $request->searchword . "%";
                    $query->where('name', 'like', $search);
                }
            }
        };

        $products = Product::where($where)
            ->orderBy('is_top', "desc")->orderBy('created_at')
            ->get();

        return $products;
    }

    /**
     * 显示
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function show(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);
        $recommends = Product::where('is_recommend', true)->where('id', '<>', $id)
            ->orderBy('is_top', 'desc')->get();

        return compact('product', 'recommends');
    }

    /**
     * 搜索
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function search()
    {
        $products = Product::where('is_recommend', true)
            ->orderBy('is_top', "desc")
            ->orderBy('created_at')
            ->get();
        return $products;
    }
}