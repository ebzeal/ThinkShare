<?php

use Illuminate\Database\Seeder;

class BlogPostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $blogPostCount = (int)$this->command->ask('How many blog posts would you like?', 50);
        $users = App\User::all();
        factory(App\BlogPost::class, $blogPostCount)->make()->each(function($post) use ($users) {
            $post->user_id = $users->random()->id;
            $post->save();
        });
    }
}
