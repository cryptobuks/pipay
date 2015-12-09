<?php namespace App\Http\Middleware;

use Closure;
use Cartalyst\Sentry\Sentry;

class Authenticate {

    /**
     * The cache of the check method.
     *
     * @var mixed
     */
    protected $cache;
    /**
     * The sentry instance.
     *
     * @var \Cartalyst\Sentry\Sentry
     */
    protected $sentry;

    /**
     * Create a new filter instance.
     *
    * @param \Cartalyst\Sentry\Sentry                        $sentry
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
        if ( !$this->check() )
        {
            if ($request->ajax())
            {
                return response('Unauthorized.', 401);
            }
            else
            {
                return redirect()->guest('user/login');
            }
        }


        return $next($request);
    }

    /**
     * Call Sentry's check method or load of cached value.
     *
     * @return bool
     */
    public function check()
    {
        if ($this->cache === null) {
            $this->cache = $this->sentry->check();
        }

        $user = $this->getUser();
        $users = $this->findGroupByName('Users');

        return $this->cache && $user && $users;
    }

    /**
     * Dynamically pass all other methods to sentry.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->sentry, $method], $parameters);
    }   

}
