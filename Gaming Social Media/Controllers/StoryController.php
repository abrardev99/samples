<?php

namespace App\Http\Controllers;

use App\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoryController
{

    public function stories()
    {
        $stories = Story::latest()->get();
        return view('story' , compact('stories'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $stories = Story::latest()->paginate(20);
        return view('admin.story.index' , compact('stories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('admin.story.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required'],
        ]);

        Story::create([
            'title' => $request->title,
            'user_id' => Auth::id(),
            'story-trixFields' => request('story-trixFields'),
            'attachment-story-trixFields' => request('attachment-story-trixFields'),
        ]);

        toast('Story created successfully' , 'success');
        return redirect()->route('admin.story.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Story  $story
     * @return View
     */
    public function show(Story $story)
    {
        return view('admin.story.show' , compact('story'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Story  $story
     * @return View
     */
    public function edit(Story $story)
    {
        return view('admin.story.edit' , compact('story'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Story  $story
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Story $story)
    {
        $request->validate([
            'title' => ['required'],
        ]);

        $story->title = $request->title;
        $story['story-trixFields'] = request('story-trixFields');
        $story['attachment-story-trixFields'] = request('attachment-story-trixFields');
        $story->save();

        toast('Story updated successfully' , 'success');
        return redirect()->route('admin.story.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Story $story)
    {
        if($story->delete()){
            toast('Story deleted successfully' , 'success');
            return redirect()->route('admin.story.index');
        }

        toast('Story deletion failed' , 'error');
        return redirect()->route('admin.story.index');
    }
}
