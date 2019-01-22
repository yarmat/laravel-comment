<?php
namespace Yarmat\Comment\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Comment extends Model
{
    use NodeTrait;

    protected $dates = ['approved_at', 'updated_at', 'created_at'];

    protected $fillable = ['approved_at', 'message', 'name', 'email', 'user_id', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(config('comment.models.user'))->withDefault();
    }

    public function scopeApproved($query)
    {
        return $query->where('approved_at', '!=', null);
    }

    public function approve()
    {
        if (!$this->isApproved()) {
            $this->approved_at = now();
            $this->timestamps = false;
            return $this->save();
        }
        return false;
    }

    public function unApprove()
    {
        if ($this->isApproved()) {
            $this->approved_at = null;
            $this->timestamps = false;
            return $this->save();
        }
        return false;
    }

    public function isApproved()
    {
        return $this->approved_at !== null;
    }

}