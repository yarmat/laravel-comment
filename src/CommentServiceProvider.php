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
            __DIR__ . '/../database/migrations/0000_00_00_000000_create_comments_table.php' => $this->getMigrationFileName('create_comments_table'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../resources/lang/en/comment.php' => resource_path('lang/vendor/yarmat/laravel-comment/en/comment.php'),
            __DIR__ . '/../resources/lang/ru/comment.php' => resource_path('lang/vendor/yarmat/laravel-comment/ru/comment.php'),
        ], 'translations');

        $this->publishes([
            __DIR__ . '/../resources/js/components/comment' => resource_path('js/components/comment')
        ], 'vue-components');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/comment.php',
            'comment'
        );

        $this->app->singleton(CommentService::class, function () {
            return new CommentService();
        });

        $this->app->alias(CommentService::class, 'comment');

    }


    private function getMigrationFileName($name)
    {
        $timestamp = date('Y_m_d_His');
        return database_path('migrations/' . $timestamp . '_' . $name . '.php');
    }
}
