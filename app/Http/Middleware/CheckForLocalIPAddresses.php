<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * MUST RESEARCH HOW LARAVEL GETS THE IP
 * MUST NOT GET SPOOFED IN ANY WAY
 */

class CheckForLocalIPAddresses
{

    private $ipRanges;
    private $ips;

    public function __construct() {
        $this->ips = explode(',', config('services.no_auth_access.single_ips') );
        $this->ipRanges = explode(',', config('services.no_auth_access.ip_ranges') );
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
            if ($this->isValidIp($ip) || $this->isValidIpRange($ip)) {
                $request->session()->put('local_ip', 1);
            } else {
                $request->session()->put('local_ip', 0);
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
        return in_array($ip, $this->ips);
    }

    /**
     * Check if the ip is in the given IP-range.
     *
     * @param $ip
     * @return bool
     */
    protected function isValidIpRange($ip)
    {
        return IpUtils::checkIp($ip, $this->ipRanges);
    }
}

