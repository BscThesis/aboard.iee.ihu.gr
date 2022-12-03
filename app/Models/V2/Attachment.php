<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['announcement_id', 'appearance_order', 'filename', 'content', 'filesize', 'mime_type'];

    /**
     * Get announcement from attachment.
     */
    public function announcement()
    {
        return $this->belongsTo('App\Models\V2\Announcement');
    }
}

