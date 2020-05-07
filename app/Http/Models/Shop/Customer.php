<?php

namespace App\Models\Shop;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;
	
    protected $fillable = ['openid', 'nickname', 'sex', 'language', 'city', 'province', 'country', 'headimgurl', 'email', 'password'];

    //一个用户有很多评论
    public function comments()
    {
        return $this->hasMany('App\Models\Shop\Comment');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Shop\Order');
    }

}
