<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];

    public function team()
    {
        return $this->belongsTo('App\Models\Crm\Team');
    }
}
