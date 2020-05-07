<?php

namespace App\Http\Controllers\Admin\Shop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Shop\Product, App\Models\Shop\Brand, App\Models\Shop\ProductGallery;
use App\Models\Shop\Category;

class ProductsController extends Controller
{

    /**
     * 商品的各种属性
     */
    private function attributes()
    {
        view()->share([
            'categories' => Category::get_categories(),
            'brands' => Brand::orderBy('sort_order')->get(),
            'filter_categories' => Category::filter_categories()
        ]);
    }

    /**
     * 商品列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {

            if ($request->has('name') and $request->name != '') {

                $search = "%" . $request->name . "%";
                $query->where('name', 'like', $search);
            }

            if ($request->has('category_id') and $request->category_id != '') {

                $category_id = $request->category_id;
                $product_ids = \DB::table('category_product')->where('category_id', $category_id)->pluck('product_id');

                $query->whereIn('id', $product_ids);
            }

            if ($request->has('is_onsale') and $request->is_onsale != '') {

                $query->where('is_onsale', $request->is_onsale);
            }

            if ($request->has('is_top') and $request->is_top == "true") {
                $query->where('is_top', true);
            }

            if ($request->has('is_recommend') and $request->is_recommend == "true") {
                $query->where('is_recommend', true);
            }

            if ($request->has('is_hot') and $request->is_hot == "true") {
                $query->where('is_hot', true);
            }

            if ($request->has('is_new') and $request->is_new == "true") {
                $query->where('is_new', true);
            }

            if ($request->has('created_at') and $request->created_at != '') {
                $time = explode(",", $request->input('created_at'));
                foreach ($time as $k => $v) {
                    $time["$k"] = $v;
                }
                $query->whereBetween('created_at', $time);
            }
        };

        $products = Product::with('categories')
            ->with('brand')
            ->where($where)
            ->orderBy('is_top', "desc")
            ->orderBy('created_at', "desc")
            ->paginate(config('admin.page_size'));

        return ['success' => true, 'msg' => '查询成功', 'data' => compact('products')];
    }


    /**
     * 新增
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function create()
    {
        $this->attributes();
        return view('admin.shop.product.create');
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
        ]);

        $product = Product::create($request->all());

        //相册
        if ($request->has('galleries')) {
            foreach ($request->galleries as $gallerie) {
                $product->product_galleries()->create(['img' => $gallerie]);
            }
        }

        //商品所属栏目
        $product->categories()->sync($request->category_id);

        return ['success' => true, 'msg' => '新增成功', 'data' => compact('product')];
    }

    /**
     * 显示一条商品
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function show($id)
    {
        $product = Product::with('brand', 'categories', 'product_galleries')->find($id);
        $this->attributes();
        return ['success' => true, 'msg' => '查询成功', 'data' => compact('product')];
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request, $id)
    {
        $this->validate($request, [
            'category_id' => 'required',
        ]);

        $product = Product::find($id);
        $product->categories()->sync($request->category_id);
        $product->update($request->all());

        if ($request->has('galleries')) {
            foreach ($product->product_galleries as $k => $v) {
                ProductGallery::destroy($v->id);
            }

            foreach ($request->galleries as $gallerie) {
                $product->product_galleries()->create(['img' => $gallerie]);
            }
        }

        return ['success' => true, 'msg' => '修改成功', 'data' => compact('product')];
    }

    /**
     * 删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy($id)
    {
        if (!Product::check_orders($id)) {
            return ['success' => false, 'msg' => '当前商品拥有对应的订单，无法删除~'];
        }
        Product::destroy($id);
        return ['success' => true, 'msg' => '被删商品已进入回收站'];
    }

    /**
     * 永久删除
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function force_destroy($id)
    {
        Product::withTrashed()->where('id', $id)->forceDelete();
        ProductGallery::where('product_id', $id)->delete();
        return ['success' => true, 'msg' => '删除成功'];
    }

    /**
     * 多选删除
     * @param Request $request
     */
    function destroy_checked(Request $request)
    {
        $checked_id = $request->input("checked_id");
        $delete_id = [];

        //检测商品是否能删除
        foreach ($checked_id as $id) {
            if (Product::check_orders($id)) {
                $delete_id[] = $id;
            }
        }

        Product::destroy($delete_id);
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
        ProductGallery::whereIn('product_id', $checked_id)->delete();
        Product::withTrashed()->whereIn('id', $checked_id)->forceDelete();
    }

    /**
     * 还原
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        Product::withTrashed()->where('id', $id)->restore();
        return ['success' => true, 'msg' => '还原成功'];
    }

    /**
     * 多选还原
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore_checked(Request $request)
    {
        $checked_id = $request->input("checked_id");
        Product::withTrashed()->whereIn('id', $checked_id)->restore();
        return ['success' => true, 'msg' => '还原成功'];
    }

    /**
     * 回收站
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function trash(Request $request)
    {
        //多条件查找
        $where = function ($query) use ($request) {

            if ($request->has('name') and $request->name != '') {

                $search = "%" . $request->name . "%";
                $query->where('name', 'like', $search);
            }

            if ($request->has('category_id') and $request->category_id != '') {

                $category_id = $request->category_id;
                $product_ids = \DB::table('category_product')->where('category_id', $category_id)->pluck('product_id');

                $query->whereIn('id', $product_ids);
            }

            if ($request->has('is_onsale') and $request->is_onsale != '') {

                $query->where('is_onsale', $request->is_onsale);
            }

            if ($request->has('is_top') and $request->is_top == "true") {
                $query->where('is_top', true);
            }

            if ($request->has('is_recommend') and $request->is_recommend == "true") {
                $query->where('is_recommend', true);
            }

            if ($request->has('is_hot') and $request->is_hot == "true") {
                $query->where('is_hot', true);
            }

            if ($request->has('is_new') and $request->is_new == "true") {
                $query->where('is_new', true);
            }

            if ($request->has('created_at') and $request->created_at != '') {
                $time = explode(",", $request->input('created_at'));
                foreach ($time as $k => $v) {
                    $time["$k"] = $v;
                }
                $query->whereBetween('created_at', $time);
            }
        };

        $products = Product::with('categories')
            ->with('brand')
            ->onlyTrashed()
            ->where($where)
            ->orderBy('is_top', "desc")
            ->orderBy('created_at', "desc")
            ->paginate(config('admin.page_size'));

        return ['success' => true, 'msg' => '查询成功', 'data' => compact('products')];
    }

    /**
     * Ajax删除相册图片
     * @param Request $request
     * @return array
     */
    function destroy_gallery(Request $request)
    {
        ProductGallery::destroy($request->gallery_id);
    }

    /**
     * 更新库存
     * @param Request $request
     * @return array
     */
    function change_stock(Request $request)
    {
        $product = Product::find($request->id);
        $product->stock = $request->stock;
        $product->save();
    }

    /**
     * Ajax修改属性
     * @param Request $request
     * @return array
     */
    function is_something(Request $request)
    {
        $attr = $request->attr;
        $product = Product::find($request->id);
        $value = $product->$attr ? false : true;
        $product->$attr = $value;
        $product->save();
    }
}
