<?php namespace App\Http\Middleware;

use Closure;
use Cartalyst\Sentry\Sentry;
use Illuminate\Http\RedirectResponse;

class RedirectIfAuthenticated {

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $sentry;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Sentry $sentry)
    {
        $this->sentry = $sentry;
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
        /*
        if (!$this->sentry->check())
        {
            return new RedirectResponse(url('/'));
        }
        */

        return $next($request);
    }

}
