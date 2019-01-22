<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 22/01/19
 * Time: 21:12
 */

namespace Yarmat\Comment\Test;


class CommentEditableTest extends TestCase
{
    public function test_edit_comment()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100)
        ]);

        $response = $this->json('POST', route('comment.update'), [
            'id' => $comment->id,
            'message' => $this->faker->realText(100)
        ]);

        $response->assertStatus(401);
    }

    public function test_edit_auth_comment()
    {
        $user = config('comment.models.user')::first();

        $blog = config('comment.models_with_comments.Blog')::first();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'user_id' => $user->id
        ]);

        $response = $this->auth()->json('POST', route('comment.update'), [
            'id' => $comment->id,
            'message' => $this->faker->realText(100)
        ]);

        $response->assertStatus(200);
    }

    public function test_edit_auth_comment_not_owner()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
        ]);

        $response = $this->auth()->json('POST', route('comment.update'), [
            'id' => $comment->id,
            'message' => $this->faker->realText(100)
        ]);

        $response->assertStatus(403);
    }

    public function test_edit_auth_comment_with_time_out()
    {
        $user = config('comment.models.user')::first();

        $blog = config('comment.models_with_comments.Blog')::first();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'user_id' => $user->id
        ]);

        $comment->created_at = $comment->created_at->subSeconds(config('comment.seconds_to_edit_own_comment') + 1);
        $comment->timestamps = false;
        $comment->save();

        $response = $this->auth()->json('POST', route('comment.update'), [
            'id' => $comment->id,
            'message' => $this->faker->realText(100)
        ]);

        $response->assertStatus(403);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals('Time to edit this comment is out', $responseData['message']);

    }

}