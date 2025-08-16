<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class AiPostGenerator
{
    protected function pollinationsUrl(string $prompt, array $params = []): string
    {
        $defaults = [
            'width'  => 1280,
            'height' => 720,
            'model'  => 'flux-realism',
            'seed'   => 420,
            'nologo' => 'true',
        ];
        $q = http_build_query(array_filter($params + $defaults, fn ($v) => $v !== null && $v !== ''));
        return 'https://image.pollinations.ai/prompt/' . urlencode($prompt) . ($q ? ('?' . $q) : '');
    }

    public function createPostFromTitle(): Post
    {
        $apiKey = env('POLLINATIONS_API_TOKEN');
        if ($apiKey == 'OPENROUTER_API_KEY') {
            $url = 'https://openrouter.ai/api/v1/chat/completions';
            $model = 'openrouter/auto';
        }else{
            $url = 'https://text.pollinations.ai/openai';
            $model = 'openai';
        }
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post($url, [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Generate a random islamic title in (10-15) bangla words ". now()
                ]
            ],
            'seed' => rand(1, 10000000),
        ]);

        if ($response->successful()) {
            $generatedText = $response->json('choices.0.message.content');
            $title = trim($generatedText, " \n\r\t\v\x00\"'"); // Clean up quotes and whitespace
        } else {
//            $this->alert('error', 'Failed to generate title.');
        }
        $apiKey = env('OPENROUTER_API_KEY');
        $usingOpenRouter = $apiKey== 'OPENROUTER_API_KEY';
        $url = $usingOpenRouter
            ? 'https://openrouter.ai/api/v1/chat/completions'
            : 'https://text.pollinations.ai/openai';
        $headers = $usingOpenRouter ? ['Authorization' => 'Bearer ' . $apiKey] : [];

        $categories = Category::orderBy('name')->get(['id','name']);
        $categoriesPayload = $categories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all();

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a strict JSON content generator. Return ONLY valid JSON with this exact schema:
{
  "excerpt": "5-7 sentence summary in Bangla",
  "content_markdown": "Full blog post in 1500 words in Bangla using Markdown (## headings, ### subheadings, lists, tips, conclusion). Avoid very long intro.",
  "tags": ["max 4 short tags in Bangla or English"],
  "cover_prompt": "A concise English prompt for image generation",
  "category": {"id": null | number, "name": null | string}
}
No extra text before or after the JSON.',
            ],
            [
                'role' => 'user',
                'content' => json_encode([
                    'title' => $title,
                    'categories' => $categoriesPayload,
                    'length' => '700-1200 words',
                    'tone' => 'helpful, clear, engaging',
                    'audience' => 'general readers',
                ]),
            ],
        ];

        $resp = Http::withHeaders($headers)
            ->timeout(90)
            ->post($url, [
                'model' => $usingOpenRouter ? 'openrouter/auto' : 'openai',
                'temperature' => 0.7,
                'messages' => $messages,
            ])
            ->throw();

        $raw = data_get($resp->json(), 'choices.0.message.content', '');
        if (!preg_match('/\{.*\}/s', (string)$raw, $m)) {
            throw new \RuntimeException('AI did not return JSON.');
        }

        $data = json_decode($m[0], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new \RuntimeException('Invalid JSON from AI.');
        }

        $excerpt = (string) data_get($data, 'excerpt', '');
        $contentMd = (string) data_get($data, 'content_markdown', '');
        $tagsArr = (array) data_get($data, 'tags', []);
        $coverPrompt = (string) data_get($data, 'cover_prompt', $title);
        $catIdAi = data_get($data, 'category.id');
        $catNameAi = data_get($data, 'category.name');

        if ($contentMd === '') {
            throw new \RuntimeException('AI did not generate content.');
        }

        // Convert Markdown -> HTML
        $converter = new CommonMarkConverter();
        $contentHtml = (string) $converter->convert($contentMd);
        $excerptFinal = Str::limit(strip_tags($excerpt ?: $contentHtml), 255);

        // Choose category (AI id -> name -> keyword -> first)
        $chosen = null;
        if (is_numeric($catIdAi)) {
            $chosen = $categories->firstWhere('id', (int) $catIdAi);
        }
        if (!$chosen && $catNameAi) {
            $chosen = $categories->first(fn ($c) => strcasecmp($c->name, (string) $catNameAi) === 0);
        }
        if (!$chosen && $categories->isNotEmpty()) {
            $lc = mb_strtolower(strip_tags($contentHtml));
            $chosen = $categories->first(fn ($c) => mb_strpos($lc, mb_strtolower($c->name)) !== false) ?: $categories->first();
        }

        // Unique slug
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $i = 2;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        // Create post
        $post = Post::create([
            'user_id' =>  1,
            'category_id' => $chosen?->id,
            'title' => $title,
            'type' => 'featured',
            'slug' => $slug,
            'content' => $contentHtml,
            'excerpt' => $excerptFinal,
            'status' => 'published',
            'meta_title' => $title,
            'meta_description' => Str::limit(strip_tags($excerptFinal), 155),
            'published_at' => now(),
        ]);

        // Tags
        $tagIds = [];
        foreach (collect($tagsArr)->map(fn ($t) => trim((string) $t))->filter()->unique()->take(8) as $tag) {
            $tagModel = \App\Models\Tag::firstOrCreate(['name' => $tag, 'slug' => Str::slug($tag)]);
            $tagIds[] = $tagModel->id;
        }
        $post->tags()->sync($tagIds);

        // Cover image (Pollinations)
        $coverUrl = $this->pollinationsUrl($coverPrompt);
        try {
            $post->addMediaFromUrl($coverUrl)
                ->usingFileName(Str::slug($title) . '.jpg')
                ->toMediaCollection('postImages');
        } catch (\Throwable $e) {
            // ignore image failure, keep the post
        }

        return $post;
    }
}
