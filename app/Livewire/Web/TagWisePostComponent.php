<?php

namespace App\Livewire\Web;

use App\Models\Post;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class TagWisePostComponent extends Component
{
    use WithPagination;

    public Tag $tag;

    public function mount(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function render()
    {
        $posts = Post::with(['category','user'])
            ->published()
            ->whereHas('tags', function ($q) {
                $q->whereKey($this->tag->id);
            })
            ->latest('published_at')
            ->paginate(12);

        $allTags = Tag::withCount(['posts' => function($q){ $q->published(); }])
            ->orderByDesc('posts_count')
            ->get();

        return view('livewire.web.tag-wise-post-component', [
            'tag' => $this->tag,
            'posts' => $posts,
            'allTags' => $allTags,
        ])->layout('components.layouts.web');
    }
}
