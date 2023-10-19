<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CacheableWithNginx
{
    /**
     * The authentication guard.
     *
     * @var string
     */
    protected $guard;
    protected array $except = [
        '/login',
        '/checkout/review',
        '/logout',
        '/goToPaymentRoute/*',
        '/checkout/*',
        '/api/login',
        '/d/*',
        '/api/v2/sms',
    ];
    private array $dontDeleteSessionExcept = [
//        '/api/v2/search',
//        '/api/v2/megaroute/getUserFormData',
//        '/api/v2/plan',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @param  null  $guard
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $this->guard = $guard;
        return tap($next($request), function ($response) use ($request) {
            if ($this->shouldAddNoCacheCookie($request)) {
                $response = $this->addCookieToResponse($request, $response, 'nocache');
            }
            if ($this->shouldAddNoDeleteSessionCookie($request)) {
                $response = $this->addCookieToResponse($request, $response, 'nodeletesession');
            }
            if ($this->shouldAddPublicCacheControlHeader($request)) {
                $response = $response->withHeaders([
                    'Cache-Control' => 'public, max-age='. 60 * (config('cache_time_in_minutes', 60)),
                ]);
            }
        });
    }

    public function shouldAddNoCacheCookie($request): bool
    {
        $requestHasUser = $this->requestHasUser($request);
        if ($requestHasUser) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param $request
     * @return bool
     */
    private function requestHasUser($request): bool
    {
        $user = $request->user($this->guard);
        return isset($user);
    }

    /**
     * Add the NoCache token to the response cookies.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @param $cookieName
     * @return Response
     */
    protected function addCookieToResponse(Request $request, Response $response, $cookieName): Response
    {
        $config = config('session');

        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        $response->headers->setCookie(
            new Cookie(
                $cookieName, '1', time() + (86400 * 30),
                $config['path'], $config['domain'], $config['secure'], false, false, $config['same_site'] ?? null
            )
        );
        return $response;
    }

    public function shouldAddNoDeleteSessionCookie($request): bool
    {
        if ($this->inExceptArray($request, $this->dontDeleteSessionExcept)) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param  Request  $request
     * @param  array  $exceptionArray
     *
     * @return bool
     */
    protected function inExceptArray($request, array $exceptionArray): bool
    {
        foreach ($exceptionArray as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }

    public function shouldAddPublicCacheControlHeader($request): bool
    {
        $requestHasUser = $this->requestHasUser($request);
        if ($requestHasUser || $this->inExceptArray($request, $this->except) || !$this->methodIsGetOrHead($request)) {
            return false;
        }
        return true;
    }

    private function methodIsGetOrHead($request): bool
    {
        return $request->isMethod('GET') || $request->isMethod('HEAD');
    }
}
