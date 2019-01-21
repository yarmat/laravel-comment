<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 21/01/19
 * Time: 21:18
 */

namespace Yarmat\Comment\Test;


use Yarmat\Comment\Models\Comment;

class CommentTest extends TestCase
{

    public function test_count()
    {
        $blog = Blog::first();

        $blog->saveComment([
            'message' => $this->faker->realText(400)
        ]);

        $response = $this->post(route('comment.count'), [
            'model' => 'Blog',
            'model_id' => $blog->id
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'count']);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($data['count'], 1);


    }

    public function test_store()
    {
        $blog = Blog::first();

        $response = $this->post(route('comment.store'), [
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($responseData['comment']['id'], $blog->comments[0]->id);

        $this->assertEquals($responseData['comment']['user']['name'], $blog->comments[0]->name);

        $this->assertEquals($responseData['comment']['user']['email'], $blog->comments[0]->email);

        $this->assertEquals($responseData['comment']['message'], $blog->comments[0]->message);

    }

    public function test_store_with_invalid_values()
    {
        $blog = Blog::first();

        $response = $this->json('POST', route('comment.store'), [
            'name' => '',
            'email' => '',
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['name', 'email']);

        $response = $this->json('POST', route('comment.store'), [
            'name' => 34643,
            'email' => $this->faker->text,
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['name', 'email']);

        $response = $this->json('POST', route('comment.store'), [
            'name' => 34643,
            'email' => $this->faker->text,
            'message' => '',
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['name', 'email', 'message']);

        $response = $this->auth()->json('POST', route('comment.store'), [
            'message' => '',
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['message']);

    }

    public function test_store_auth()
    {
        $blog = Blog::first();

        $response = $this->auth()->post(route('comment.store'), [
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertStatus(200);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($responseData['comment']['id'], $blog->comments[0]->id);

        $this->assertEquals($responseData['comment']['user']['name'], $blog->comments[0]->user->name);

        $this->assertEquals($responseData['comment']['user']['email'], $blog->comments[0]->user->email);

        $this->assertEquals($responseData['comment']['message'], $blog->comments[0]->message);
    }

    public function test_delete()
    {
        $comment = (Blog::first())->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'parent_id' => 0
        ]);

        $response = $this->auth()->post(route('comment.destroy'), [
            'id' => $comment->id
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'message']);
    }

    public function test_update()
    {

        $comment = (Blog::first())->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'parent_id' => 0
        ]);

        $response = $this->post(route('comment.update'), [
            'id' => $comment->id,
            'message' => $this->faker->realText(100)
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'message']);

    }
}