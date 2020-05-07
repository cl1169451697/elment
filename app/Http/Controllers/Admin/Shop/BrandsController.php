<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop\Brand;

class BrandsController extends Controller
{

    /**
     * 品牌列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $where = function ($query) use ($request) {
            if ($request->has('keyword') and $request->keyword != '') {
                $search = "%" . $request->keyword . "%";
                $query->where('name', 'like', $search);
            }
        };

        $brands = Brand::where($where)->orderBy('sort_order')->paginate(4);
        return ['success' => true, 'msg' => '', 'data' => compact('brands')];
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $brand = Brand::create($request->all());
        return ['success' => true, 'msg' => '新增成功', 'data' => compact('brand')];
    }

    /**
     * 显示
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $brand = Brand::find($id);
        return ['success' => true, 'msg' => '查询成功', 'data' => compact('brand')];
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        $brand->update($request->all());
        return ['success' => true, 'msg' => '更新成功', 'data' => compact('brand')];
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Brand::destroy($id);
        return ['success' => true, 'msg' => '删除成功'];
    }

    /**
     * Ajax排序
     * @param Request $request
     * @return array
     */
    function sort_order(Request $request)
    {
        $brand = Brand::find($request->id);
        $brand->sort_order = $request->sort_order;
        $brand->save();
        $brands = Brand::orderBy('sort_order')->paginate(4);
        return ['success' => true, 'msg' => '', 'data' => compact('brands')];
    }

    /**
     * Ajax修改属性
     * @param Request $request
     * @return array
     */
    function is_something(Request $request)
    {
        $attr = $request->attr;
        $brand = Brand::find($request->id);
        $value = $brand->is_show ? false : true;
        $brand->$attr = $value;
        $brand->save();
    }
}
