<?php

namespace App\Livewire\Web;

use App\Models\Category;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryWisePostComponent extends Component
{
    use WithPagination;

    public Category $category;

    public function mount(Category $category): void
    {
        $this->category = $category;
    }

    public function render()
    {
        $posts = Post::with(['category', 'user'])
            ->published()
            ->where('category_id', $this->category->id)
            ->latest('published_at')
            ->paginate(12);

        $allCategories = Category::withCount(['posts' => function ($q) {
            $q->published();
        }])->orderByDesc('posts_count')->get();

        return view('livewire.web.category-wise-post-component', [
            'category' => $this->category,
            'posts' => $posts,
            'allCategories' => $allCategories,
        ])->layout('components.layouts.web');
    }
}
