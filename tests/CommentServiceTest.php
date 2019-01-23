<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 21/01/19
 * Time: 21:18
 */

namespace Yarmat\Comment\Test;


class CommentServiceTest extends TestCase
{
    public function test_config()
    {
        $blog = $this->firstBlog();

        $config = \Comment::config('Blog', $blog->id);

        $this->assertTrue(strpos($config, 'name') != false);
        $this->assertTrue(strpos($config, 'email') != false);
        $this->assertTrue(strpos($config, 'locale') != false);
        $this->assertTrue(strpos($config, 'lang') != false);
        $this->assertTrue(strpos($config, 'order') != false);
        $this->assertTrue(strpos($config, 'isUserLogged') != false);
        $this->assertTrue(strpos($config, 'prefix') != false);
        $this->assertTrue(strpos($config, 'model') != false);
        $this->assertTrue(strpos($config, 'model_id') != false);
    }

    public function test_get_model()
    {
        $model = \Comment::getModel('Blog');

        $this->assertTrue($model !== false);

        $model = \Comment::getModel('Test');

        $this->assertFalse($model);
    }
}