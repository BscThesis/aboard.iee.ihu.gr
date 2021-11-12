<?php

namespace App\Auth;
// namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\Auth\UserProvider;

class MyUserProvider implements UserProvider
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    public function retrieveByCredentials(array $credentials) {}
    public function retrieveByToken($identifier, $token) {}
    public function updateRememberToken($user, $token) {}
    public function validateCredentials($user, array $credentials) {}

}