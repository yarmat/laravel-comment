<?php

namespace Yarmat\Comment\Http\Middleware;

use Closure;
use Yarmat\Comment\Contracts\CommentContract;

class CommentMiddleware
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
        $modelName = $request->get('model');

        if(! is_null($modelName)) {
            $interfaces = class_implements($modelName);

            if (! isset($interfaces[CommentContract::class])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your model has not implement ' . CommentContract::class
                ], 500);
            }
        }

        return $next($request);
    }
}
