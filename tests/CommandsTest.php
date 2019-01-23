<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 23/01/19
 * Time: 20:34
 */

namespace Yarmat\Comment\Test;


class CommandsTest extends TestCase
{
    public function test_clear_all()
    {
        $this->saveCommentToFirstBlog();

        $this->artisan('comment:clear');

        $this->assertTrue(is_null($this->firstComment()));
    }
}