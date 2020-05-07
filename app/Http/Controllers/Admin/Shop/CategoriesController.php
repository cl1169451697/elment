<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Shop\Category;

class CategoriesController extends Controller
{
    /**
     * 分类列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        $categories = Category::get_categories();
        Category::clear();
        return ['success' => true, 'msg' => '', 'data' => compact('categories')];
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $category = Category::create($request->all());
        Category::clear();
        return ['success' => true, 'msg' => '新增成功', 'data' => compact('category')];
    }

    /**
     * 查询一条
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $category = Category::find($id);
        return ['success' => true, 'msg' => '查询成功', 'data' => compact('category')];
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request, $id)
    {
        $category = Category::find($id);
        $category->update($request->all());
        Category::clear();
        return ['success' => true, 'msg' => '更新成功', 'data' => compact('brand')];
    }


    /**
     * Ajax修改属性
     * @param Request $request
     * @return array
     */
    function is_something(Request $request)
    {
        $attr = $request->attr;
        $category = Category::find($request->id);
        $value = $category->$attr ? false : true;
        $category->$attr = $value;
        $category->save();
        Category::clear();
    }

    /**
     * Ajax排序
     * @param Request $request
     * @return array
     */
    function sort_order(Request $request)
    {
		$request->validate([
		    'sort_order' => 'required|digits_between:1,2',
		]);
        $category = Category::find($request->id);
        $category->sort_order = $request->sort_order;
        $category->save();
        Category::clear();
        $categories = Category::get_categories();
        return ['success' => true, 'msg' => '', 'data' => compact('categories')];
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy($id)
    {
        if (!Category::check_children($id)) {
            return ['success' => false, 'msg' => '当前分类有子分类，请先将子分类删除后再尝试删除~', 'data' => compact('brand')];
        }

//        if (!Category::check_products($id)) {
//            return ['success' => true, 'msg' => '当前分类有商品，请先将对应商品删除后再尝试删除~', 'data' => compact('brand')];
//
//        }

        Category::destroy($id);
        Category::clear();
        return ['success' => true, 'msg' => '删除成功'];
    }
}
