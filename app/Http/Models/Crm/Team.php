<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Team extends Model
{
    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(Team::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Team::class, 'parent_id', 'id');
    }

    public function students()
    {
        return $this->hasMany('App\Models\Crm\Student');
    }

    static function get_teams()
    {
        $teams = Cache::rememberForever('itcasts_admin_teams', function () {
            return self::with([
                'children' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('id');
                },
            ])->where('parent_id', 0)->orderBy('sort_order')->orderBy('id')->get();
        });
        return $teams;
    }

    /**
     * 清除缓存
     */
    static function clear()
    {
        Cache::forget('itcasts_admin_teams');
    }

    /**
     * 排序
     * @param $id
     * @param $parent_id
     * @param $sort_order
     */
    static function sort_order($id, $parent_id, $sort_order)
    {
        $team = self::find($id);
        $team->sort_order = $sort_order;
        $team->parent_id = $parent_id;
        $team->save();
    }

    /**
     * 检查是否有子栏目
     */
    static function check_children($id)
    {
        $team = self::with('children')->find($id);
        if ($team->children->isEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * 检查是否有课程
     */
    static function check_students($id)
    {
        $team = self::with('students')->find($id);
        if ($team->students->isEmpty()) {
            return true;
        }
        return false;
    }
}
