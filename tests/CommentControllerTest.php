<?php
/**
 * Created by PhpStorm.
 * User: yarmat
 * Date: 21/01/19
 * Time: 21:18
 */

namespace Yarmat\Comment\Test;

use Yarmat\Comment\Contracts\CommentContract;

class CommentControllerTest extends TestCase
{

    public function test_count()
    {
        $blog = $this->firstBlog();

        $blog->saveComment([
            'message' => $this->faker->realText(400),
            'approved_at' => now()
        ]);

        $response = $this->post(route('comment.count'), [
            'model' => 'Blog',
            'model_id' => $blog->id
        ]);

        $response->assertStatus(200);;
        $response->assertJsonStructure(['success', 'count', 'message']);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($responseData['count'], 1);


    }

    public function test_store()
    {
        $blog = $this->firstBlog();

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

    public function test_store_as_children()
    {
        $blogModel = \Comment::getModel('Blog');

        $blog = $blogModel::first();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100)
        ]);

        $response = $this->json('POST', route('comment.store'), [
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => $comment->id
        ]);

        $responseData = json_decode($response->getContent(), true);

        $commentNew = config('comment.models.comment')::whereId($responseData['comment']['id'])->first();

        $this->assertTrue($comment->isRoot());

        $this->assertTrue($commentNew->isChildOf($comment));
    }

    public function test_store_with_invalid_values()
    {
        $blog = $this->firstBlog();

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
        $blog = $this->firstBlog();

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

    public function test_store_auth_with_spam_word()
    {
        $blog = $this->firstBlog();

        $response = $this->auth()->json('POST', route('comment.store'), [
            'message' => 'spam is my life',
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['message']);
    }

    public function test_store_with_spam_word()
    {
        $blog = $this->firstBlog();

        $response = $this->json('POST', route('comment.store'), [
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => 'spam is my life',
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['message']);
    }

    public function test_store_with_bad_site()
    {
        $blog = config('comment.models_with_comments.Blog')::first();

        $response = $this->auth()->json('POST', route('comment.store'), [
            'message' => 'http://bad.site',
            'model' => 'Model',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['message']);
    }


    public function test_store_to_model_without_contract()
    {

        $response = $this->json('POST', route('comment.store'), [
            'message' => $this->faker->realText(100),
            'model' => 'News',
            'model_id' => 1,
            'parent_id' => 0
        ]);

        $response->assertStatus(500);

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($responseData['message'], 'Your model has not implement ' . CommentContract::class);

    }


    public function test_delete()
    {
        $blog = $this->firstBlog();

        $comment = $blog->saveComment([
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
        $user = $this->firstUser();

        $blog = $this->firstBlog();

        $comment = $blog->saveComment([
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->realText(100),
            'user_id' => $user->id,
            'parent_id' => 0
        ]);

        $response = $this->auth()->post(route('comment.update'), [
            'id' => $comment->id,
            'message' => $this->faker->realText(100)
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'message']);

    }

    public function test_allowable_sites()
    {
        $blog = $this->firstBlog();

        $response = $this->json('POST', route('comment.store'), [
            'name' => $this->faker->firstName,
            'email' => $this->faker->email,
            'message' => $this->faker->url,
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['message']);

        $response = $this->auth()->json('POST', route('comment.store'), [
            'message' => $this->faker->url,
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertJsonValidationErrors(['message']);

        $response = $this->auth()->json('POST', route('comment.store'), [
            'message' => 'https://vk.com',
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => 0
        ]);

        $response->assertStatus(200);
    }

    public function test_get()
    {
        $blog = $this->firstBlog();

        $response = $this->json('POST', route('comment.get'), [
            'page' => 1,
            'model' => 'Blog',
            'model_id' => $blog->id,
            'parent_id' => null
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['success', 'message', 'comments']);
    }

}