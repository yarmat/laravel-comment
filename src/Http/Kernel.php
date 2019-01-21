<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 21/01/19
 * Time: 21:37
 */

namespace Yarmat\Comment\Http;


use Illuminate\Auth\Middleware\Authenticate;
use Orchestra\Testbench\Http\Middleware\RedirectIfAuthenticated;
use Yarmat\Comment\Http\Middleware\CommentMiddleware;

class Kernel extends \Orchestra\Testbench\Http\Kernel
{
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'comment' => CommentMiddleware::class
    ];
}