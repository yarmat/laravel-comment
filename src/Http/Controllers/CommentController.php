<?php

namespace Yarmat\Comment\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use Jenssegers\Date\Date;
use Yarmat\Comment\Http\Requests\CommentRequest;
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
        $modelName = \Comment::getModel($request->get('model'));

        $modelId = $request->get('model_id');

        $parentId = $request->get('parent_id');

        $page = $request->get('page');

        $take = config('comment.limit');

        $skip = ($page - 1) * $take;

        $comments = $this->commentModelName::where('commentable_id', $modelId)
            ->where('commentable_type', $modelName)
            ->where('parent_id', $parentId)
            ->with(config('comment.comment_relations'))
            ->with(['descendants' => function ($query) use ($request) {
                $query->with(config('comment.comment_relations'))
                    ->orderBy('created_at', $request->get('order'));
            }])
            ->skip($skip)
            ->take($take)
            ->orderBy('created_at', $request->get('order'))
            ->get();

        $nextComment = $this->commentModelName::where('commentable_id', $modelId)
            ->where('commentable_type', $modelName)
            ->where('parent_id', $parentId)
            ->skip($skip + $take)
            ->first(['id']);

        $commentsTree = $this->commentsToTree($comments);

        return response()->json([
            'success' => true,
            'message' => 'Comments is loaded',
            'comments' => $this->transformTree($commentsTree),
            'isVisibleMoreButton' => !is_null($nextComment)
        ]);
    }

    public function store(CommentRequest $request)
    {
        $this->saveAuthorToCookies($request->get('name'), $request->get('email'));

        $modelName = \Comment::getModel($request->get('model'));

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
        $modelName = \Comment::getModel($request->get('model'));

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
        return config('comment.transformFunction')($item);
    }

    private function commentsToTree($comments)
    {
        foreach($comments as $key => $comment) {
            $comments[$key]->children = $comment->descendants->toTree();
        }

        return $comments;
    }

    private function transformTree($items)
    {
        $transformItems = [];

        foreach($items as $key => $item) {
            $transformItems[$key] = $this->transformItem($item);
            if(count($item->children) > 0 ) {
                $transformItems[$key]['children'] = $this->transformTree($item->children);
            };
        }

        return $transformItems;
    }

    private function saveAuthorToCookies($name, $email)
    {
        if(\Auth::check()) return false;

        \Cookie::queue(\Cookie::make('author-comment', json_encode([
            'name' => $name,
            'email' => $email
        ]), 60*60*24*1, '/'));

    }

}
