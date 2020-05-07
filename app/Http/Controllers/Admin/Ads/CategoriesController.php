<?php

namespace App\Http\Controllers\Admin\Ads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Ads\Category;

class CategoriesController extends Controller
{
	/**
	* 分类列表
	* @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	*/
	function index()
	{
		$categories = Category::get_categories();
		return ['success' => true, 'msg' => '查询成功', 'data' => compact('categories')];
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
		return ['success' => true, 'msg' => '编辑成功', 'data' => compact('category')];	
	}

	/**
	* Ajax排序
	* @param Request $request
	* @return array
	*/
	function sort_order(Request $request)
	{
		$category = Category::find($request->id);
		$category->sort_order = $request->sort_order;
		$category->save();
		Category::clear();
	}

	/**
	* 删除
	* @param $id
	* @return \Illuminate\Http\RedirectResponse
	*/
	function destroy($id)
	{
		if (!Category::check_ads($id)) {
	        return ['success' => false, 'msg' => '当前分类有广告，请先将对应广告删除后再尝试删除~', ];
		}

		Category::destroy($id);
		Category::clear();
        return ['success' => true, 'msg' => '删除成功'];
	}
}
