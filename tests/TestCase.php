<?php

namespace Yarmat\Comment\Test;

use Illuminate\Foundation\Testing\WithFaker;
use Jenssegers\Date\Date;
use Jenssegers\Date\DateServiceProvider;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Yarmat\Comment\CommentServiceProvider;
use Yarmat\Comment\Facades\Comment;
use Yarmat\Comment\Test\Models\Blog;
use Yarmat\Comment\Test\Models\User;

class TestCase extends BaseTestCase
{
    use WithFaker;

    protected function getPackageProviders($app)
    {
        return [
            CommentServiceProvider::class,
            NestedSetServiceProvider::class,
            DateServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Comment' => Comment::class,
            'Date' => Date::class
        ];
    }

    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', 'Yarmat\Comment\Http\Kernel');
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));
        $this->loadMigrationsFrom(realpath(__DIR__ . '/database/migrations'));
        $this->seeding();
    }

    protected function seeding()
    {
        $this->seedUser();
        $this->seedBlog();
    }

    protected function seedUser()
    {
        User::create([
            'name' => 'Test',
            'email' => 'test@mail.ru',
            'password' => \Hash::make('secret')
        ]);
    }

    protected function seedBlog()
    {
        Blog::create([
            'content' => $this->faker->realText(500)
        ]);
    }

    protected function auth()
    {
        $user = User::first();

        return $this->actingAs($user);
    }

}