<?php
namespace Yarmat\Comment;

class CommentServcice {
    public function config($modelName, $modelId)
    {
        $authorComment = json_decode(\Cookie::get('author-comment'), true);
        
        $config = [
            'name' => is_array($authorComment) ?  $authorComment['name'] : '',
            'email' => is_array($authorComment) ?  $authorComment['email'] : '',
            'locale' => \App::getLocale(),
            'order' => config('comment.default_order'),
            'model' => $modelName,
            'model_id' => $modelId,
            'prefix' => config('comment.prefix'),
            'isUserLogged' => \Auth::check(),
            'lang' => __('comment')
        ];

        $script = '<script>';
        $script .= 'window.Comment = JSON.parse(`' . json_encode($config) . '`);';
        $script .= '</script>';

        return $script;
    }

    public function getModel($modelName)
    {
        $models = config('comment.models_with_comments');

        if(array_key_exists($modelName, $models)) return $models[$modelName];

        return false;
    }
}