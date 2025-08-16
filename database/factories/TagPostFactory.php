<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Tag;
use App\Models\TagPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TagPost>
 */
class TagPostFactory extends Factory
{
    protected $model = TagPost::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'tag_id' => Tag::factory(),
        ];
    }
}

