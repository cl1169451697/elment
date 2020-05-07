<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['name', 'desc', 'url', 'thumb', 'sort_order', 'is_show'];
}
