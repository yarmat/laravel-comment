<?php

namespace Yarmat\Comment\Facades;

use Illuminate\Support\Facades\Facade;

class Comment extends Facade
{
    /**
     * @see \Spatie\Menu\Laravel\Menu
     */
    protected static function getFacadeAccessor() : string
    {
        return 'comment';
    }
}
