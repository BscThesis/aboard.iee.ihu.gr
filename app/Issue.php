<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'body'];

    /**
     * Get the user that owns the issue.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
