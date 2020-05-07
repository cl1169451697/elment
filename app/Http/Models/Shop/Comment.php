<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //每个评论都属于某一个商品
    public function good()
    {
        return $this->belongsTo('App\Models\Shop\Good');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Shop\User');
    }
}
