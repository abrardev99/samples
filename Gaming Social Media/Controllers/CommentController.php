<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Post;
use App\Traits\ViiManagerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use ViiManagerTrait;

    public function index(Post $post)
    {
        return view('welcome-show-post' , compact('post'));
    }

    public function store(Request  $request)
    {
        $this->validate($request, [
            'post_id' => 'required',
        ]);
        if(request('comment-trixFields')['body'] == null){
            toast('You should write something to comment' , 'error');
            return redirect()->back();
        }

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $request->post_id,
            'comment-trixFields' => request('comment-trixFields'),
            'attachment-comment-trixFields' => request('attachment-comment-trixFields'),
        ]);

        $this->storeVii();

        return redirect()->back();
    }
}
