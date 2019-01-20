<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 19/01/19
 * Time: 21:22
 */

namespace Yarmat\Comment\Traits;


trait CommenterTrait
{
    public function comments()
    {
        return $this->hasMany(config('comment.models.comment'));
    }
}