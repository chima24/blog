<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $name = $this->faker->name();

        return [
            'title' => $name,
            'slug' => Str::slug($name),
            'category_id' => function () {
                return Category::factory()->create()->id;
            },
            'owner_id' => function () {
                return User::factory()->create()->id;
            },
            'body' => $this->faker->sentence(200),
        ];
    }
}
