<?php

return [
    'limit' => '10',

    'default_order' => 'desc',

    'models' => [
        'user' => App\User::class,
        'comment' => \Yarmat\Comment\Models\Comment::class
    ],

    'controller' => \Yarmat\Comment\Http\Controllers\CommentController::class,

    'prefix' => 'comments',

    'middleware' => [

        'store' => ['auth'],

        'delete' => ['auth'],

        'get' => [],

        'update' => []
    ]
];