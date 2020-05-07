<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Auth;
use GatewayClient\Gateway;

class Chat extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\System\User');
    }

    //绑定用户信息
    static function bind($request)
    {
        Gateway::$registerAddress = '127.0.0.1:1238';

        $id = Auth::id();
        $name = Auth::user()->name;
        $avatar = Auth::user()->avatar;
        Gateway::bindUid($request->client_id, $id);
        Gateway::joinGroup($request->client_id, session('room_id'));
        Gateway::setSession($request->client_id, compact('id', 'name', 'avatar'));
    }

    //初始化数据库中的数据
    static function chats()
    {
        $result['type'] = 'bind';
        $chats = Chat::with('user')->where('room_id', session('room_id'))
            ->orderBy('id', 'desc')->limit(15)->get();

        $result['chats'] = $chats->map(function ($item, $key) {
            return [
                'name' => $item->user->name,
                'avatar' => $item->user->avatar,
                'content' => $item->content,
                'time' => $item->created_at->format("Y-m-d H:i:s")
            ];
        });

        Gateway::sendToUid(Auth::id(), json_encode($result));
//        sendToCurrentClient
    }

    // 提示加入聊天室
    static function login()
    {
        $result = [
            'type' => 'say',
            'chat' => [
                'name' => Auth::user()->name,
                'avatar' => Auth::user()->avatar,
                'content' => '加入了聊天室',
                'time' => date('Y-m-d H:i:s')
            ],
        ];

        Gateway::sendToGroup(session('room_id'), json_encode($result));
    }

    //发送当前用户列表
    static function clients()
    {
        $result = [
            'type' => 'clients',
            'clients' => Gateway::getClientSessionsByGroup(session('room_id'))
        ];

        Gateway::sendToGroup(session('room_id'), json_encode($result));
    }

    static function say($request)
    {
        $result = [
            'type' => 'say',
            'chat' => [
                'name' => Auth::user()->name,
                'avatar' => Auth::user()->avatar,
                'content' => $request->get('content'),
                'time' => date('Y-m-d H:i:s')
            ]
        ];

        if ($request->client_id) {
            $result['chat']['name'] = Auth::user()->name . ' 对 ' . User::find($request->client_id)->name . ' 说： ';
            Gateway::sendToUid(Auth::id(), json_encode($result));
            Gateway::sendToUid($request->client_id, json_encode($result));
            return;
        }

        //公共信息存入数据库
        Gateway::sendToGroup(session('room_id'), json_encode($result));
    }
}
