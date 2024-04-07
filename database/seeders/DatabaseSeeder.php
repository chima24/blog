<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory(10)->create();

        $increment = 1;

        $users->each(function ($user) use (&$increment) {
            if ($increment % 2 === 0) {
                Category::factory()->create(['user_id' => $user->id]);
            }
            $increment++;
        });

        for ($i = 0; $i < 50; $i++) {
            Post::factory()->create([
                'owner_id'    => random_int(1, 10),
                'category_id' => random_int(1, 5),
            ]);
        }

        for ($i = 0; $i < 100; $i++) {
            Comment::factory()->create([
               'owner_id' => random_int(1, 10),
               'post_id' => random_int(1, 50),
            ]);
        }
    }
}
