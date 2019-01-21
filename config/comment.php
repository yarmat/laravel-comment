<?php

return [
    'limit' => '5',

    'default_order' => 'DESC',

    'models' => [
        'user' => \Yarmat\Comment\Test\User::class,
        'comment' => \Yarmat\Comment\Models\Comment::class
    ],

    'controller' => \Yarmat\Comment\Http\Controllers\CommentController::class,

    'prefix' => 'comments',

    'middleware' => [

        'store' => ['throttle:15'],

        'destroy' => ['auth'],

        'get' => [],

        'update' => [],

        'count' => []
    ],

    'models_with_comments' => [
        'Blog' => \Yarmat\Comment\Test\Blog::class
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
            'message' => 'required|string'
        ],
        'not_auth' => [
            'name' => 'required|alpha',
            'email' => 'required|email',
            'message' => 'required|string'
        ],
        'messages' => []
    ],


    'transformFunction' => function ($item) {
        return [
            'id' => $item->id,
            'message' => $item->message,
            'isVisibleForm' => false,
            'date' => \Date::parse($item->created_at)->diffForHumans(),
            'user' => [
                'name' => $item->user->name ?? $item->name,
                'email' => $item->user->email ?? $item->email
            ],
            'children' => []
        ];
    },

    'allowable_tags' => ''
];
