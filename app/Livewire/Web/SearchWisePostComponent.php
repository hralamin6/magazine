<?php

namespace App\Livewire\Web;

use App\Models\Post;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class SearchWisePostComponent extends Component
{
    use WithPagination;

    public string $q = '';

    public function mount(Request $request): void
    {
        $this->q = trim((string) $request->query('q', ''));
//        dd($this->q);
    }

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $posts = Post::with(['category', 'user'])
            ->published()
            ->when($this->q !== '', function ($query) {
                $term = '%' . str_replace('%', '\\%', $this->q) . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                      ->orWhere('excerpt', 'like', $term)
                      ->orWhere('content', 'like', $term)
                      ->orWhereHas('user', function ($u) use ($term) {
                          $u->where('name', 'like', $term);
                      })
                      ->orWhereHas('tags', function ($t) use ($term) {
                          $t->where('name', 'like', $term);
                      });
                });
            })
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('livewire.web.search-wise-post-component', [
            'posts' => $posts,
            'q' => $this->q,
        ])->layout('components.layouts.web');
    }
}
