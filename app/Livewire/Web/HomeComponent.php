<?php

namespace App\Livewire\Web;

use App\Models\Category;
use App\Models\Tag;
use Barryvdh\Debugbar\Facades\Debugbar;
use Livewire\Component;
use App\Models\Post;
use Livewire\WithPagination;

class HomeComponent extends Component
{
    use WithPagination;

    public function render()
    {
        $featured_posts = Post::with(['category'])->where('type', 'featured')
            ->published()
            ->latest('published_at')
            ->take(5)
            ->get();

        $most_view_posts = Post::with(['user'])
            ->published()
            ->latest('published_at')
            ->take(5)
            ->get();

        $latest_posts = Post::with(['category','user'])
            ->published()
            ->latest('published_at')
            ->paginate(9)->withQueryString();;

        $categories = Category::withCount(['posts' => function($q){ $q->published(); }])
            ->orderByDesc('posts_count')
//            ->take(10)
            ->get();

        $tags = Tag::withCount(['posts' => function($q){ $q->published(); }])
            ->orderByDesc('posts_count')
//            ->take(20)
            ->get();

        return view('livewire.web.home-component', compact('featured_posts','most_view_posts','latest_posts','categories','tags'))
            ->layout('components.layouts.web');
    }
}
