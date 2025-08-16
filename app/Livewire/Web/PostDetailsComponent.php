<?php

namespace App\Livewire\Web;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PostDetailsComponent extends Component
{
    use LivewireAlert;

    public Post $post;

    public bool $liked = false;
    public int $likesCount = 0;

    public function mount(Post $post): void
    {
        $this->post = $post->load(['category','user','media','tags']);
        $this->trackUniqueView();
        // initialize likes
        $this->likesCount = $this->post->likedUsers()->count();
        $this->liked = $this->post->isLikedBy(Auth::user());
    }
    protected function trackUniqueView(): void
    {
        $user = auth()->user();
        $visitor = $user
            ? 'user:' . $user->id
            : 'guest:' . hash('sha256', (string) request()->ip() . '|' . (string) request()->userAgent());

        // daily-unique key for this post+visitor
        $key = "post:{$this->post->id}:visitor:{$visitor}:d1";

        // only the first hit in 24h increments
        if (Cache::add($key, true, now()->addDay())) {
            $this->post->increment('views'); // atomic DB increment
//            $this->post->views++;            // keep in-memory model in sync
        }
    }

    public function toggleLike(): void
    {
        $user = Auth::user();
        if (!$user) {
            $this->alert('error', __('Please login to like this post.'));
            return;
        }
        if ($this->liked) {
            $this->post->likedUsers()->detach($user->id);
            $this->liked = false;
            $this->likesCount = max(0, $this->likesCount - 1);
        } else {
            $this->post->likedUsers()->syncWithoutDetaching([$user->id]);
            $this->liked = true;
            $this->likesCount++;
        }
    }

    public function render()
    {
        $relatedPosts = Post::with(['category','user'])
            ->published()
            ->where('category_id', $this->post->category_id)
            ->where('id', '!=', $this->post->id)
            ->orderBy('views', 'desc')
            ->latest('published_at')
            ->take(3)
            ->get();
        $userPosts = Post::with(['category','user'])
            ->published()
            ->where('user_id', $this->post->user->id)
            ->where('id', '!=', $this->post->id)
            ->orderBy('views', 'desc')
            ->latest('published_at')
            ->take(3)
            ->get();

        $prevPost = Post::where('published_at', '<', $this->post->published_at)->published()->orderBy('published_at', 'desc')->first();
        $nextPost = Post::where('published_at', '>', $this->post->published_at)->published()->orderBy('published_at', 'asc')->first();

        return view('livewire.web.post-details-component', [
            'post' => $this->post,
            'relatedPosts' => $relatedPosts,
            'prevPost' => $prevPost,
            'nextPost' => $nextPost,
            'userPosts' => $userPosts,
        ])->layout('components.layouts.web');
    }
}
