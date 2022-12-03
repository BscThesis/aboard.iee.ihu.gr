<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use \Symfony\Component\HttpKernel\Exception\HttpException;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'last_login_at', 'is_author', 'is_admin', 'id', 'uid', 'name_eng'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_admin' => false,
        'is_author' => false
    ]; 

    /**
     * Get the user's subscribed tags.
     */
    public function subscriptions()
    {
        return $this->belongsToMany('App\Models\V1\Tag');
    }

    /**
     * Get the user's announcements.
     */
    public function announcements()
    {
        return $this->hasMany('App\Models\V1\Announcement');
    }

    /**
     * Get the users notifications.
     */
    public function activities()
    {
        return $this->hasMany('App\Models\V1\Notification', 'notifiable_id', 'id');
    }

    /**
     * Get issues submitted by a user.
     */
    public function issues()
    {
        return $this->hasMany('App\Models\V1\Issue');
    }
}
