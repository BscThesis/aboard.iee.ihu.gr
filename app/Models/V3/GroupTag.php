<?php

namespace App\Models\V3;

use Illuminate\Database\Eloquent\Model;

class GroupTag extends Model
{
    protected $table = 'group_tag';

    protected $fillable = [
        'group_id',
        'tag_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
