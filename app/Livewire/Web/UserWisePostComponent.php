<?php

namespace App\Livewire\Web;

use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserWisePostComponent extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function render()
    {
        $posts = Post::with(['category','user'])
            ->published()
            ->where('user_id', $this->user->id)
            ->latest('published_at')
            ->paginate(12);

        $allUsers = User::withCount(['posts' => function($q){ $q->published(); }])
            ->orderByDesc('posts_count')
            ->get();

        return view('livewire.web.user-wise-post-component', [
            'user' => $this->user,
            'posts' => $posts,
            'allUsers' => $allUsers,
        ])->layout('components.layouts.web');
    }
}
