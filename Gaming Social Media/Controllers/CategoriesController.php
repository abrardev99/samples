<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Categories::all();
        return view('admin.categories.index' , compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required' , 'string']
        ]);

        $category = new Categories();
        $category->user_id = Auth::id();
        $category->name = $request->name;
        $category->save();

        toast('Category added successfully' , 'success');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function show(Categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categories $categories)
    {
        //
    }


    public function destroy($categoryId)
    {
        Post::where('category_id' , $categoryId)->delete();
        $category = Categories::findOrFail($categoryId);
        $category->delete();

        toast('Category deleted successfully' , 'success');
        return redirect()->back();
    }
}
