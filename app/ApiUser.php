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
    protected $appends = ['is_admin', 'is_author'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'last_login_at',
        'id',
        'uid',
        'name_eng'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function groups()
    {
        return $this->belongsToMany(\App\Models\V3\Group::class, 'user_has_group', 
            'user_id',
            'group_id'
        )->withPivot(['role'])->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasMany(\App\Models\V3\Announcement::class, 'user_id', 'id');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(\App\Models\V3\Tag::class, 'tag_user', 'user_id', 'tag_id');
    }

    public function activities()
    {
        return $this->hasMany(\App\Models\V3\Notification::class, 'notifiable_id', 'id');
    }

    public function issues()
    {
        return $this->hasMany(\App\Models\V3\Issue::class);
    }

    public function scopeWithAnyRole($q, array $roles)
    {
        return $q->whereHas('groups', fn($g) =>
            $g->whereIn('user_has_group.role', $roles)
        );
    }

    public function scopeAuthors($q)
    {
        return $q->withAnyRole(['staff', 'admin']);
    }

    public function isAdmin(): bool
    {
        return $this->groups()->wherePivot('role', 'admin')->exists();
    }

    public function isAuthor(): bool
    {
        return $this->groups()->whereIn('user_has_group.role', ['staff','admin'])->exists();
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    public function getIsAuthorAttribute(): bool
    {
        return $this->isAuthor();
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
