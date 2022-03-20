<?php

namespace App\Models;

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
        return $this->belongsToMany('App\Models\Announcement');
    }

    public function children()
    {
        return $this->hasMany('App\Models\Tag', 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\Models\Tag', 'id', 'parent_id');
    }

    public function childrenRecursive()
    {
        // Return each Tag children counting every announcement each one has
        return $this->children()->withCount(['announcements'=>function ($query){
            $query->withFilters(
                request()->input('users', []),
                request()->input('tags', []),
                json_decode(request()->input('title', '')),
                json_decode(request()->input('body', '')),
                json_decode(request()->input('updatedAfter', '')),
                json_decode(request()->input('updatedBefore', '')),
            );
        }])->with('childrenRecursive');        
    }

    public function childrensubRecursive()
    {
        return $this->children()->with('childrensubRecursive');
    }

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User')->select('id', 'name', 'name_eng', 'email');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'is_public', 'parent_id', 'maillist_name'];
}
