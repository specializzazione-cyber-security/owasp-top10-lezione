<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class BlockSuspiciousIPs
{
    protected $maxAttempts = 5;
    protected $decayMinutes = 1;
    protected $blockMinutes = 1;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();

        $key = $this->throttleKey($ip);
        
        if (Cache::has($key . ':blocked')) {
            Session::flash('errors', "Your IP has been blocked for $this->blockMinutes minute(s) due to suspicious activity.");
            return redirect()->back();
            // return response('Your IP has been blocked due to suspicious activity.', Response::HTTP_TOO_MANY_REQUESTS);
        }
        if (Cache::has($key)) {
            $attempts = Cache::increment($key);
            if ($attempts > $this->maxAttempts) {
                Cache::put($key . ':blocked', true, $this->blockMinutes * 60);
                Log::warning("IP $ip has been blocked for $this->blockMinutes minute(s) due to too many requests.");
                Session::flash('errors', "Your IP has been blocked for $this->blockMinutes minute(s) due to suspicious activity.");
                return redirect()->back();
                //return response('Your IP has been blocked due to suspicious activity.', Response::HTTP_TOO_MANY_REQUESTS);
            }
        } else {
            Cache::put($key, 1, $this->decayMinutes * 60);
        }
        return $next($request);
    }
    
    protected function throttleKey($ip)
    {
        return 'throttle:' . sha1($ip);
    }
}