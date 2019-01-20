<?php

namespace Yarmat\Comment\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Yarmat\Comment\Models\Comment;

class CommentController extends Controller
{
    public $commentModelName;

    public function __construct()
    {
        $this->commentModelName = config('comment.models.comment');
    }

    public function get(Request $request)
    {
        $parent = $request->get('parent');
        $take = config('comment.limit');
        $skip = $request->get('skip');
        $order = $request->get('order');
        $modelName = $request->get('model');
        $modelId = $request->get('model_id');

        $query = $this->commentModelName::where('commentable_id', $modelId)
            ->where('commentable_type', $modelName)
            ->where('parent_id', $parent)
            ->with(['user.profile', 'descendants' => function ($query) use ($order) {
                $query->with(['user.profile'])
                    ->withCount('upLikes')
                    ->withCount('downLikes');

                switch ($order) {
                    case 'likes':
                        $query->orderBy('up_likes_count', 'DESC');
                        break;
                    case 'date-old':
                        $query->orderBy('created_at', 'ASC');
                        break;
                    default:
                        $query->orderBy('created_at', 'DESC');
                }

                if (\Auth::check()) $query->with('likeOfCurrentUser'); // If user Auth, join Active Like
            }])
            ->withCount('upLikes')
            ->withCount('downLikes')
            ->take($take)
            ->skip($skip);


        if (\Auth::check()) $query->with('likeOfCurrentUser'); // If user Auth, join Active Like

        switch ($order) {
            case 'likes':
                $query->orderBy('up_likes_count', 'DESC');
                break;
            case 'date-old':
                $query->orderBy('created_at', 'ASC');
                break;
            default:
                $query->orderBy('created_at', 'DESC');
        }
//        return $query->get();
        $comments = $this->commentPrepare($this->prepareChildren($query->get()));

        $nextCommentId = Comment::where('commentable_id', $id)
            ->where('commentable_type', Content::class)
            ->where('parent_id', $parent)
            ->skip($skip + self::COMMENTS_LIMIT)
            ->first(['id']);

        return response()->json([
            'success' => true,
            'items' => $comments,
            'isVisibleMoreButton' => !is_null($nextCommentId)
        ]);

    }

    public function store(Request $request)
    {
        $modelName = $request->get('model');
        $modelId = $request->get('model_id');

        $model = $modelName::whereId($modelId)->firstOrFail();

        $comment = $model->saveComment([
            'message' => $request->get('message'),
            'parent_id' => $request->get('parent_id'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'user_id' => \Auth::user()->id ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment saved',
            'comment' => $this->transformItem($comment)
        ]);

    }

    public function update(Request $request)
    {
        $comment = $this->commentModelName::whereId($request->get('id'))->firstOrFail();

        $comment->update([
            'message' => $request->get('message')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment is updated'
        ]);
    }

    public function destroy(Request $request)
    {
        $this->commentModelName::destroy($request->get('id'));

        return response()->json([
           'success' => true,
           'message' => 'Comment is deleted'
        ]);
    }

    public function count(Request $request)
    {
        $modelName = $request->get('model');

        $modelId = $request->get('model_id');

        $commentCount =  $this->commentModelName::where('commentable_id', $modelId)
            ->where('commentable_type', $modelName)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $commentCount
        ]);
    }

    private function transformItem(Comment $item)
    {
        return [
            'id' => $item->id,
            'message' => $item->message,
            'isVisibleForm' => false,
            'date' => Date::parse($item->created_at)->diffForHumans(),
            'user' => [
                'name' => $item->user->name,
                'email' => $item->user->email
            ],
            'children' => []
        ];
    }

}
