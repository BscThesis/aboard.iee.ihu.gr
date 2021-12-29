<?php namespace App\Auth\Social\Two;
 
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class IeeProvider extends AbstractProvider implements ProviderInterface  {
    /**
     * Login Iee Ihu API endpoint
     *
     * @var string
     */
    protected $apiUrl = 'api.iee.ihu.gr';

    /**
     * The scopes being requested
     *
     * @var array
     */
    protected $scopes = ['profile'];

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://login.iee.ihu.gr/authorization/', $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://login.iee.ihu.gr/token';
    }

     /**
     * Get the raw user for the given access token.
     *
     * @param  string $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        $userUrl = 'https://api.iee.ihu.gr/profile';

        $response = $this->getHttpClient()->get(
            $userUrl, $this->getRequestOptions($token)
        );

        $user = json_decode($response->getBody(), true);

        return $user;
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array $user
     * @return \Laravel\Socialite\User
     */
    protected function mapUserToObject(array $user)
    {

        $group = $user['edupersonaffiliation'];
        $name_gr = $group === "staff" ? $user['cn;lang-el'] : Str::upper($user['cn;lang-el']);
        $name_eng = !empty(Str::title($user['cn'])) ? Str::title($user['cn']) : Str::ascii($user['cn;lang-el']);
        $is_author = $group === "staff";
        $email = $user['edupersonnickname'];

        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'name'     => $name_gr,
            'name_eng' => $name_eng,
            'email'    => $email,
            'is_author'   => $is_author
        ]);
    }
}