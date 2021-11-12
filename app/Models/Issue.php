<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', 'body'];

    /**
     * Get the user that owns the issue.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
