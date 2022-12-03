<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ApiUser extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    
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
        return $this->belongsToMany('App\Models\V2\Tag');
    }

    /**
     * Get the user's announcements.
     */
    public function announcements()
    {
        return $this->hasMany('App\Models\V2\Announcement');
    }

    /**
     * Get the users notifications.
     */
    public function activities()
    {
        return $this->hasMany('App\Models\V2\Notification', 'notifiable_id', 'id');
    }

    /**
     * Get issues submitted by a user.
     */
    public function issues()
    {
        return $this->hasMany('App\Models\V2\Issue');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
