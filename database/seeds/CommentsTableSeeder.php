<?php

use App\BlogPost;
Use App\Comment;
use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = BlogPost::all();

        if($posts->count() <= 0 ) {
            $this->command->info('No comments will be added as there are no blog posts');
            return;
        }
        
        $commentCount = (int)$this->command->ask('How many commentswould you like?', 150);
        
        $users = App\User::all();
        
        factory(Comment::class, $commentCount)->make()->each(function ($comment) use ($posts, $users) {
            $comment->blog_post_id = $posts->random()->id;
            $comment->user_id = $users->random()->id;
            $comment->save();
        });
    }
}
