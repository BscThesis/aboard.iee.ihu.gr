<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function announcements()
    {
        return $this->belongsToMany('App\Announcement');
    }

    public function children()
    {
        return $this->hasMany('App\Tag', 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\Tag', 'id', 'parent_id');
    }

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'is_public', 'parent_id'];
}
