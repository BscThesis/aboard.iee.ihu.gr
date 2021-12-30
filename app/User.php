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
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    // public function findForPassport($username)
    // {
    //     return $this->where('uid', $username)->first();
    // }

    /**
     * Bypass Laravel Passport's default auth logic.
     *
     * @var array
     */
    public function findAndValidateForPassport($username, $password)
    {
        
        // Set attributes for Laravel
        $attributes = [
            'id' => $user->id
        ];

        return new static($attributes);
    }

    /**
     * Get the user's subscribed tags.
     */
    public function subscriptions()
    {
        return $this->belongsToMany('App\Models\Tag');
    }

    /**
     * Get the user's announcements.
     */
    public function announcements()
    {
        return $this->hasMany('App\Models\Announcement');
    }

    /**
     * Get the users notifications.
     */
    public function activities()
    {
        return $this->hasMany('App\Models\Notification', 'notifiable_id', 'id');
    }

    /**
     * Get issues submitted by a user.
     */
    public function issues()
    {
        return $this->hasMany('App\Models\Issue');
    }
}
