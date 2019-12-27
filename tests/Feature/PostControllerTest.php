<?php

namespace Tests\Feature;

use App\BlogPost;
use App\Comment;


use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function testemptyBlogPostTable() {
        $response = $this->get('/posts');

        $response->assertSeeText('No posts here');
    }

    // public function createSampleBlogPost(): BlogPost {
    //     $post = new BlogPost();
    //     $post->title = 'New title';
    //     $post->content = 'Content of the blog post';
    //     $post->save();

    //     return $post;
    // }

    public function createSampleBlogPost(): BlogPost {
        // $post = new BlogPost();
        // $post->title = 'New title';
        // $post->content = 'Content of the blog post';
        // $post->save();

        return factory(BlogPost::class)->states('new-title')->create();

        // return $post;
    }

    public function testOneBlogPostWithNoComments() {
        $post = $this->createSampleBlogPost();

        $response = $this->get('/posts');
        $response->assertSeeText('New title');
        $response->assertSeeText('No Comments yet');

        // you can also do database assertions
        $this->assertDatabaseHas('blog_posts', [
            'title' => 'New title'
        ]);
    }

    public function testOneBlogPostWithCommnets() {
        $post = $this->createSampleBlogPost();
        factory(Comment::class, 4)->create([
            'blog_post_id' => $post->id
        ]);

        $response = $this->get('/posts');

        $response->assertSeeText('4 comments');
    }

    public function testAddNewBlogPost() {
        // $user = $this->user();
        
        $params = [
            'title' => 'Valid title',
            'content' => 'At least 10 characters'
        ];
        // $this->actingAs($user);

        // 302 is status code for successful redirect
        $this->actingAs($this->user()) // for user authentication
            ->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('status');

            $this->assertEquals(session('status'), 'Blog post was created');
    }

    public function testStoreFail() {
        $params = [
            'title' => 'a',
            'content' => 'a'
        ];

        $this->actingAs($this->user())
        ->post('/posts', $params)
        ->assertStatus(302)
        ->assertSessionHas('errors');

        $messages = session('errors')->getMessages();
        $this->assertEquals($messages['title'][0], 'The title must be at least 5 characters.');
        $this->assertEquals($messages['content'][0], 'The content must be at least 10 characters.');
    }

    public function testUpdateValid()
    {
        $post = $this->createSampleBlogPost();
        $this->assertDatabaseHas('blog_posts', $post->toArray());
        $params = [
            'title' => 'A new named title',
            'content' => 'Content was changed'
        ];
        $this->actingAs($this->user())
            ->put("/posts/{$post->id}", $params)
            ->assertStatus(302)
            ->assertSessionHas('status');
        $this->assertEquals(session('status'), 'Blog post was updated');
        $this->assertDatabaseMissing('blog_posts', $post->toArray());
        $this->assertDatabaseHas('blog_posts', [
            'title' => 'A new named title'
        ]);
}

    public function testDelete() {
        $post = $this->createSampleBlogPost();
        $this->assertDatabaseHas('blog_posts', $post->toArray());
        
        $this->actingAs($this->user())
        ->delete("/posts/{$post->id}")
        ->assertStatus(302)
        ->assertSessionHas('status');

        $this->assertSoftDeleted('blog_posts', $post->toArray());
        
        $this->assertEquals(session('status'), 'Blog post was deleted');
        // $this->assertDatabaseMissing('blog_posts', $post->toArray());
    }

}