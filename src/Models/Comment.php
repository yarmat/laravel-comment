<?php
namespace Yarmat\Comment\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;

class Comment extends Model
{

    use NodeTrait;
    use Cachable {
        Cachable::newEloquentBuilder insteadof NodeTrait;
    }

    public function newEloquentBuilder($query) {
        return new QueryBuilder($query);
    }

    protected $dates = ['verified_at', 'updated_at', 'created_at'];
    protected $fillable = ['message', 'name', 'email', 'user_id', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(config('comment.models.user'))->withDefault();
    }

}