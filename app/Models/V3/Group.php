<?php

namespace App\Models\V3;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
        protected $fillable = [
        'name',
        'description',
        'parent_group',
        'is_user',
        'is_author',
    ];

    /**
     * Users that belong to the group.
     */
    public function users()
    {
        return $this->belongsToMany(\App\ApiUser::class, 'user_has_group',
            'group_id',
            'user_id'
        )->withPivot('role')->withTimestamps();
    }

    /**
     * Parent group relationship.
     */
    public function parentGroup()
    {
        return $this->belongsTo(self::class, 'parent_group');
    }

    /**
     * Subgroups relationship.
     */
    public function subgroups()
    {
        return $this->hasMany(self::class, 'parent_group');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'group_tag', 'group_id', 'tag_id')->withTimestamps();
    }
}
