<?php

namespace App\Models\V2;

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
        return $this->belongsToMany('App\Models\V2\Announcement');
    }

    public function children()
    {
        return $this->hasMany('App\Models\V2\Tag', 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\Models\V2\Tag', 'id', 'parent_id');
    }

    public function childrenRecursive()
    {
        // Return each Tag children counting every announcement each one has
        return $this->children()->withCount(['announcements'=>function ($query){
            $query->tags(
                request()->input('users', []),
                request()->input('tags', []),
                json_decode(request()->input('title', '')),
                json_decode(request()->input('body', '')),
                json_decode(request()->input('updatedAfter', '')),
                json_decode(request()->input('updatedBefore', '')),
            );
        }])->having('announcements_count','>',0)->with('childrenRecursive');        
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
        return $this->belongsToMany('App\ApiUser', 'tag_user', 'tag_id', 'user_id')->select('id', 'name', 'name_eng', 'email')->whereNull('deleted_at');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'is_public', 'parent_id', 'maillist_name'];
}