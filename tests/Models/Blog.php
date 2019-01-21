<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 21/01/19
 * Time: 22:16
 */

namespace Yarmat\Comment\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Yarmat\Comment\Contracts\CommentContract;
use Yarmat\Comment\Traits\HasCommentTrait;

class Blog extends Model implements CommentContract
{
    use HasCommentTrait;

    protected $fillable = ['content'];
}