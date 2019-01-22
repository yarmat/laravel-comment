<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 21/01/19
 * Time: 21:18
 */

namespace Yarmat\Comment\Test;


class CommentApproveTest extends TestCase
{
    public function test_approve()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $comment = $blog->saveComment([
            'message' => $this->faker->realText(100)
        ]);

        $this->assertFalse($comment->isApproved());

        $comment->approve();

        $this->assertTrue($comment->isApproved());

    }

    public function test_un_approve()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $comment = $blog->saveComment([
            'message' => $this->faker->realText(100)
        ]);

        $comment->approve();

        $this->assertTrue($comment->isApproved());

        $comment->unApprove();

        $this->assertFalse($comment->isApproved());

    }

    public function test_store()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $response = $this->json('POST', route('comment.store'), [
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertFalse($responseData['comment']['is_approved']);
    }

    public function test_store_auth()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $response = $this->auth()->json('POST', route('comment.store'), [
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertTrue($responseData['comment']['is_approved']);
    }

}