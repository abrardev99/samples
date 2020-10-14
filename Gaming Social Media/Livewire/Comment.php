<?php

namespace App\Http\Livewire;

use App\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Comment extends Component
{
    public $post;
    public $comment;
    public $canComment = false;
    public function mount($post)
    {
        $this->post = $post;
    }

    public function updatedComment()
    {
        if(strlen($this->comment) > 0)
            $this->canComment = true;
        else
            $this->canComment = false;
    }


    public function save()
    {
        $post = Post::findOrFail($this->post);
        $post->commentAsUser(Auth::user(), $this->comment);
        $this->comment = null;
    }

    public function destroy($id)
    {
        $comment = \App\Comment::findOrFail($id);
        $comment->delete();
    }

    public function toggleLike($commentId)
    {
        if(Auth::check()){
        $comment = \App\Comment::findOrFail($commentId);
        $user = Auth::user();
        $user->toggleLike($comment);
        }
        else{
            return redirect()->route('login');
        }
    }

    public function render()
    {
        $post = Post::findOrFail($this->post);
        $comments = $post->comments;
        return view('livewire.comment', compact('comments'));
    }
}
