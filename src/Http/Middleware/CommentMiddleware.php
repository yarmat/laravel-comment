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
        $modelName = \Comment::getModel($request->get('model'));
        $locale = $request->get('locale');

        if(!is_null($locale)) {
            \App::setLocale($locale);
            \Date::setLocale($locale);
        }

        if($modelName) {
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
