<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function likeToggle(Post $post)
    {
        $user = Auth::user();
        $user->toggleLike($post);
        return redirect()->back();
    }
}
