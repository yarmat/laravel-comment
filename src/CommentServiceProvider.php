<?php

namespace Yarmat\Comment;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/comment.php' => config_path('comment.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_comments_table.php' => $this->getMigrationFileName(),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/../resources/lang/en/comment.php' => resource_path('lang/en/comment.php'),
            __DIR__.'/../resources/lang/ru/comment.php' => resource_path('lang/ru/comment.php'),
        ], 'langs');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CommentServcice::class, function() {
           return new CommentServcice();
        });

        $this->app->alias(CommentServcice::class, 'comment');

    }


    private function getMigrationFileName()
    {
        $timestamp = date('Y_m_d_His');
        return database_path('migrations/' . $timestamp . '_' . 'create_comments_table.php');
    }
}
