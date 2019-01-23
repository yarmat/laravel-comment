<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 23.01.19
 * Time: 9:47
 */

namespace Yarmat\Comment\Test;


class CommentTimeToUpdateTest extends TestCase
{
    public function test_no_time()
    {
        $blog = $this->firstBlog();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'parent_id' => 0
        ]);

        $comment->created_at = $comment->created_at->subSeconds(config('comment.seconds_to_edit_own_comment') + 1);

        $comment->timestamps = false;

        $comment->save();

        $this->assertFalse($comment->isTimeToUpdate());
    }

    public function test_is_time()
    {
        $blog = $this->firstBlog();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'parent_id' => 0
        ]);

        $this->assertTrue($comment->isTimeToUpdate());
    }
}