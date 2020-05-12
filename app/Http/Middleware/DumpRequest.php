<?php

namespace App\Http\Middleware;

use Closure;
use Develpr\AlexaApp\Alexa;

class DumpRequest
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
        logger(json_encode($request->url()));
//        logger(json_encode($request->all()));
        logger(\Alexa::say('hello'));
        return $next($request);
    }
}
