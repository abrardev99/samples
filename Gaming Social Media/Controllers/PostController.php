<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Post;
use App\Traits\ViiManagerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    use ViiManagerTrait;

    public function index()
    {
        $role = Auth::user()->getRoleNames()->first();
        $posts = null;
        if($role == 'admin')
         $posts = Post::with('likes')->paginate(20);

        if($role == 'user')
         $posts = Post::with('likes')->where('user_id' , Auth::id())->paginate(20);

        return view('post.index' , compact('posts'));
    }


    public function create()
    {
        $categories = Categories::all();
        return view('post.create' , compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required'],
            'category' => ['required' , Rule::notIn(0)],
            'publish' => ['required' ],
        ]);

        $publishedAt = null;

        if($request->publish == 1){
            $publishedAt = Carbon::now();
        }

        Post::create([
            'title' => $request->title,
            'user_id' => Auth::id(),
            'category_id' => $request->category,
            'published_at' => $publishedAt,
            'post-trixFields' => request('post-trixFields'),
            'attachment-post-trixFields' => request('attachment-post-trixFields'),
        ]);

//        award vii = 5
        $this->storeVii();

        toast('Post created successfully' , 'success');
        return redirect()->route('post.index');

    }


    public function show(Post $post)
    {
        return view('post.show' , compact('post'));
    }


    public function edit(Post $post)
    {
        $categories = Categories::all();
        return view('post.edit' , compact('post', 'categories'));
    }


    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => ['required'],
            'category' => ['required'],
            'publish' => ['required' ],
        ]);

        $publishedAt = null;

        if($request->publish == 1){
            $publishedAt = Carbon::now();
        }

        $post->title = $request->title;
        $post->category_id = $request->category;
        $post->published_at = $publishedAt;
        $post['post-trixFields'] = request('post-trixFields');
        $post['attachment-post-trixFields'] = request('attachment-post-trixFields');
        $post->save();

        toast('Post updated successfully' , 'success');
        return redirect()->route('post.index');
    }


    public function destroy(Post $post)
    {
        $post->delete();
        toast('Post delete successfully' , 'success');
        return redirect()->back();
    }
}
