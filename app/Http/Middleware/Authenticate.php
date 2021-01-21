<?php

namespace App\Http\Middleware;

use App\CodeReponse;
use App\Exceptions\BusinessException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * @param Request $request
     * @param array $guards
     * @throws BusinessException
     * @throws AuthenticationException
     */
    protected function unauthenticated($request, array $guards){
        if($request->expectsJson() || in_array('wx',$guards)){
            throw new BusinessException(CodeReponse::UN_LOGIN);
        }
        parent::authenticate($request,$guards);
    }
}
