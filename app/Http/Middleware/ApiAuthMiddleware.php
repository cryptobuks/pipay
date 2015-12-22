<?php

namespace App\Http\Middleware;

use Closure;
use App\UserKey;
use Illuminate\Support\Facades\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $credentials = $request->only('api_key','livemode');

        if( !$user = UserKey::authenticate( $credentials ) ) {
            $response = Response::json ( [
                'error' => 'Unauthorized'  ],
                401
            );
            
            $response->header('Content-Type', 'application/json');
            return $response;
        }

        return $next($request);
    }
}
