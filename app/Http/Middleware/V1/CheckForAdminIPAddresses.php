<?php

namespace App\Http\Middleware\V1;

use Closure;

class CheckForAdminIPAddresses
{
    private $ip;

    public function __construct() {
        $this->ip = config('services.only_admin_access');
   }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach ($request->getClientIps() as $ip) {
            if ($this->isValidIp($ip)) {
                $request->session()->put('admin_ip', 1);
            } else {
                $request->session()->put('admin_ip', 0);
            }
        }
	    return $next($request);
    }

    /**
     * Check if the given IP is valid.
     *
     * @param $ip
     * @return bool
     */
    protected function isValidIp($ip)
    {
        return in_array($ip, $this->ip);
    }
}
