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
        // Get values for LDAP server, try to connect and set some options
        $ldapconfig['host'] = config('services.ldap.host');
        $ldapconfig['port'] = config('services.ldap.port');
        $ldapconfig['basedn'] = config('services.ldap.base_dn');
        $ldapconfig['usersdn'] = explode(',', config('services.ldap.users_dn'));

        $ds = ldap_connect($ldapconfig['host'], $ldapconfig['port']);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ds, LDAP_OPT_NETWORK_TIMEOUT, 10);

        $name_gr = '';
        $name_eng = '';
        $email = '';
        $is_author = false;

        if (isset($username)) {
            $bind = 0;
            foreach ($ldapconfig['usersdn'] as $value) {
                // Build user to bind
                $dn = "uid=" . $username . "," . $value . "," . $ldapconfig['basedn'];
                if (!$bind) {
                    // Try to bind to LDAP server
                    $bind = @ldap_bind($ds, $dn, $password);
                }
            }

            if (!$bind) {
                throw new HttpException(401, 'Invalid credentials');
            }

            try {
                // Search based on this attribute
                $filter = "(" . config('services.ldap.filter_attribute') . "=$username)";

                // Get only these attributes
                $attr = explode(',', config('services.ldap.search_attributes'));

                // Perform the search and get results
                $sr = ldap_search($ds, $ldapconfig['basedn'], $filter, $attr);
                $info = ldap_get_entries($ds, $sr);

                // Get the results we need
                $name_gr = Str::upper($info['0']['cn;lang-el']['0']);
                $name_eng = !empty(Str::upper($info['0']['cn']['0'])) ? Str::upper($info['0']['cn']['0']) : Str::ascii($info['0']['cn;lang-el']['0']);
                $group = $info['0']['edupersonaffiliation']['0'];
                $is_author = false;
                if ($group === "staff") {
                    $is_author = true;
                }
                $email = $info['0']['edupersonnickname']['0'];
            } catch (Exception $e) {
                return false;
            }
        }

        // Create or update user based on our results
        $user = User::where('uid', $username)->first();
        if ($user === null) {
            $user = User::create(
                [
                    'name' => $name_gr,
                    'name_eng' => $name_eng,
                    'email' => $email,
                    'uid' => $username,
                    'is_author' => $is_author
                ]
            );
        } else {
            $user = User::where('uid', $username)->update(
                [
                    'name' => $name_gr,
                    'name_eng' => $name_eng,
                    'email' => $email,
                    'uid' => $username,
                    'is_author' => $is_author
                ]
            );
        }
        $user = User::where('uid', $username)->first();

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
        return $this->belongsToMany('App\Tag');
    }

    /**
     * Get the user's announcements.
     */
    public function announcements()
    {
        return $this->hasMany('App\Announcement');
    }

    /**
     * Get the users notifications.
     */
    public function activities()
    {
        return $this->hasMany('App\Notification', 'notifiable_id', 'id');
    }

    /**
     * Get issues submitted by a user.
     */
    public function issues()
    {
        return $this->hasMany('App\Issue');
    }
}
