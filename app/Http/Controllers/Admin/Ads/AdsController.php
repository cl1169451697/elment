<?php

namespace App\Http\Controllers\Admin\Ads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Ads\Ad;
use App\Models\Ads\Category;


class AdsController extends Controller
{
    /**
     * 广告列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //查找
        $where = function ($query) use ($request) {
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
        };

        $ads = Ad::with('category')->where($where)->orderBy('created_at')->paginate(config('admin.page_size'));
		return ['success' => true, 'msg' => '查询成功', 'data' => compact('ads')];
    }

   
    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function store(Request $request)
    {
        $ad = Ad::create($request->all());
		return ['success' => true, 'msg' => '新增成功', 'data' => compact('ad')];
    }

    /**
     * 更新
     * @param Request $request
     * @param Ad $ad
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request, Ad $ad)
    {
        $ad->update($request->all());
		return ['success' => true, 'msg' => '编辑成功', 'data' => compact('ad')];
    }

    /**
     * 删除到回收站
     * @param Ad $ad
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy(Ad $ad)
    {
        $ad->delete();
        return ['success' => true, 'msg' => '被删广告已进入回收站'];
    }

    /**
     * 永久删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function force_destroy($id)
    {
        Ad::withTrashed()->where('id', $id)->forceDelete();
        return ['success' => true, 'msg' => '删除成功'];
    }

    /**
     * 多选删除到回收站
     * @param Request $request
     * @return array
     */
    function destroy_checked(Request $request)
    {
        $checked_id = $request->input("checked_id");
        Ad::destroy($checked_id);
        return ['success' => true, 'msg' => '删除成功'];
    }

    /**
     * 多选永久删除
     * @param Request $request
     * @return array
     */
    function force_destroy_checked(Request $request)
    {
        $checked_id = $request->input("checked_id");
        Ad::withTrashed()->whereIn('id', $checked_id)->forceDelete();
    }

    /**
     * 还原
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        Ad::withTrashed()->where('id', $id)->restore();
        return ['success' => true, 'msg' => '还原成功'];
    }

    /**
     * 多选还原
     * @param Request $request
     * @return array
     */
    public function restore_checked(Request $request)
    {
        $checked_id = $request->input("checked_id");
        Ad::withTrashed()->whereIn('id', $checked_id)->restore();
        return ['success' => true, 'msg' => '还原成功'];
    }

    /**
     * 回收站
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function trash()
    {
        $ads = Ad::with('category')->onlyTrashed()->paginate(config('admin.page_size'));
        return ['success' => true, 'msg' => '查询成功', 'data' => compact('ads')];
    }

    /**
     * Ajax排序
     * @param Request $request
     * @return array
     */
    function sort_order(Request $request)
    {
        $ad = Ad::find($request->id);
        $ad->sort_order = $request->sort_order;
        $ad->save();
    }
}
