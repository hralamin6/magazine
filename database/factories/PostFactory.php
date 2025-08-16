<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, // Random user
            'category_id' => Category::inRandomOrder()->first()->id, // Random category
            'title' => $this->faker->sentence,
            'slug' => Str::slug($this->faker->sentence),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->sentence,
            'tags' => json_encode($this->faker->words(3)),
            'type' => $this->faker->randomElement(['featured', 'normal']),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->sentence,
            'published_at' => $this->faker->randomElement([now(), null]), // Random publish date or null
        ];
    }
}
