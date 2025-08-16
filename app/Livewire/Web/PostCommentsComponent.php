<?php

namespace App\Livewire\Web;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PostCommentsComponent extends Component
{
    use LivewireAlert;

    public Post $post;

    #[Validate('required|string|min:2|max:5000')]
    public string $body = '';

    public ?int $replyTo = null; // parent comment id when replying

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function startReply(int $commentId): void
    {
        $this->replyTo = $commentId;
        $this->body = '';
        // request the frontend to focus the textarea
        $this->dispatch('focus-comment');
    }

    public function cancelReply(): void
    {
        $this->replyTo = null;
        $this->body = '';
    }

    public function addComment(): void
    {
        if (!Auth::check()) {
            $this->alert('error', __('Please login to to comment.'));
            return;
        }

        $this->validate();

        Comment::create([
            'post_id'   => $this->post->id,
            'user_id'   => Auth::id(),
            'parent_id' => $this->replyTo,
            'body'      => $this->body,
            'status'    => 'active',
        ]);

        // reset form
        $this->body = '';
        $this->replyTo = null;
        $this->alert('success', __('Comment added successfully!'));
    }

    public function deleteComment(int $commentId): void
    {
        if (!Auth::check()) return;

        $comment = Comment::where('post_id', $this->post->id)->find($commentId);
        if (!$comment) return;

        $userId = Auth::id();
        $isOwner = $comment->user_id === $userId;
        $isPostAuthor = $this->post->user_id === $userId;

        if ($isOwner || $isPostAuthor) {
            $comment->delete();
            $this->alert('success', __('Comment deleted successfully!'));
        } else {
            $this->alert('error', __('You can only delete your own comments or comments on your posts.'));
        }
    }

    public function render()
    {
        $comments = $this->post
            ->topLevelComments()
            ->with(['user', 'children.user', 'children.children.user'])
            ->get();

        $totalCount = $this->post->comments()->count();

        return view('livewire.web.post-comments-component', [
            'comments'   => $comments,
            'totalCount' => $totalCount,
            'post'       => $this->post,
        ]);
    }
}
