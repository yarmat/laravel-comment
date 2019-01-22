<?php

return [
    'limit' => '5',

    'default_order' => 'DESC',

    'models' => [
        'user' => \Yarmat\Comment\Test\Models\User::class,
        'comment' => \Yarmat\Comment\Models\Comment::class
    ],

    'controller' => \Yarmat\Comment\Http\Controllers\CommentController::class,

    'prefix' => 'comments',

    'middleware' => [

        'store' => ['throttle:15'],

        'destroy' => ['auth'],

        'get' => [],

        'update' => ['auth'],

        'count' => []
    ],

    'models_with_comments' => [

        'Blog' => \Yarmat\Comment\Test\Models\Blog::class,

        'News' => \Yarmat\Comment\Test\Models\News::class

    ],

//    'comment_relations' => ['user' => function($query) {
//        $query->select(['id', 'name', 'email']);
//    }, 'likes' => function($query){
//        $query-> ...
//    }],

    'comment_relations' => ['user'],


    // Validation
    'validation' => [
        'auth' => [
            'message' => ['required', 'string', new \Yarmat\Comment\Rules\Spam(), new \Yarmat\Comment\Rules\AllowableSite()]
        ],
        'not_auth' => [
            'name' => 'required|alpha',
            'email' => 'required|email',
            'message' => ['required', 'string', new \Yarmat\Comment\Rules\Spam(), new \Yarmat\Comment\Rules\AllowableSite()]
        ],
        'messages' => []
    ],


    'transformFunction' => function ($item) {
        return [
            'id' => $item->id,
            'message' => $item->message,
            'isVisibleForm' => false,
            'date' => \Date::parse($item->created_at)->diffForHumans(),
            'is_approved' => $item->isApproved(),
            'user' => [
                'name' => $item->user->name ?? $item->name,
                'email' => $item->user->email ?? $item->email
            ],
            'children' => []
        ];
    },

    'allowable_tags' => '',

    'spam_list' => ['spam'],

    'allowable_sites' => ['test.com', 'vk.com'],

    'approved_quest' => false,

    'seconds_to_edit_own_comment' => 60 * 5 // 5 minutes. Only for auth users

];
