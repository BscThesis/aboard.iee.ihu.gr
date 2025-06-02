<?php

namespace App\Models\V3;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description'];

    /**
     * Users that belong to the group.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_has_group')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Parent group relationship (self-join).
     */
    public function parentGroup()
    {
        return $this->belongsTo('App\Models\Group', 'parent_group');
    }

    /**
     * Subgroups relationship (self-join).
     */
    public function subgroups()
    {
        return $this->hasMany('App\Models\Group', 'parent_group');
    }
}
