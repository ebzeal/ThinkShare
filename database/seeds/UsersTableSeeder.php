<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userCount = max((int)$this->command->ask('How many users would you like?', 20), 1);
        factory(User::class)->states('john-doe')->create();
        factory(User::class, $userCount)->create();

    }
}
