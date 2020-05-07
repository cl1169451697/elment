<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhotoController extends Controller
{
    /**
     * 文件上传类
     * @param Request $request
     * @return array
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('file') and $request->file('file')->isValid()) {

            //数据验证
            $allow = array('image/jpeg', 'image/png', 'image/gif');

            $mine = $request->file('file')->getMimeType();
            if (!in_array($mine, $allow)) {
                return response()->json(['status' => 0, 'msg' => '文件类型错误，只能上传图片']);
            }

            //文件大小判断$filePath
            $max_size = 1024 * 1024 * 3;
            $size = $request->file('file')->getClientSize();
            if ($size > $max_size) {
                return response()->json(['status' => 0, 'msg' => '文件大小不能超过3M']);
            }

            //original图片
            $path = $request->file->store('images');

            //绝对路径
            $file_path = storage_path('app/') . $path;

            //保存到七牛
            qiniu_upload($file_path);

            //返回文件名
            $image = basename($path);

            return ['status' => 1, 'image' => $image, 'image_url' => env('QINIU_IMAGES_LINK') . $image];
        }
    }
}
