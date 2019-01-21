<?php
namespace Yarmat\Comment\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Comment extends Model
{
    use NodeTrait;

    protected $dates = ['verified_at', 'updated_at', 'created_at'];
    protected $fillable = ['message', 'name', 'email', 'user_id', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(config('comment.models.user'))->withDefault();
    }

}