<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Shop\Express;

class ExpressesController extends Controller
{
    /**
     * 快递列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $expresses = Express::orderBy('sort_order')->paginate(config('admin.page_size'));

         return ['success' => true, 'msg' => '查询成功', 'data' => compact('expresses')];
    }



    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
       $express= Express::create($request->all());
         return ['success' => true, 'msg' => '新增成功', 'data' => compact('express')];
    }

    /**
     * 编辑
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function  show($id)
    {
        $express = Express::find($id);
       return ['success' => true, 'msg' => '查询成功', 'data' => compact('express')];
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $express = Express::find($id);
        $express->update($request->all());
      return ['success' => true, 'msg' => '修改成功', 'data' => compact('express')];
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        Express::destroy($id);
         return ['success' => true, 'msg' => '删除成功'];
    }

    /**
        * Ajax排序
        * @param Request $request
        * @return array
        */
       function sort_order(Request $request)
       {
           $express = Express::find($request->id);
           $express->sort_order = $request->sort_order;
           $express->save();
           $expresses = Express::orderBy('sort_order')->paginate(4);
           return ['success' => true, 'msg' => '', 'data' => compact('expresses')];
       }

       /**
        * Ajax修改属性
        * @param Request $request
        * @return array
        */
       function is_something(Request $request)
       {
           $attr = $request->attr;
           $express = Express::find($request->id);
           $value = $express->is_show ? false : true;
           $express->$attr = $value;
           $express->save();
       }
}
