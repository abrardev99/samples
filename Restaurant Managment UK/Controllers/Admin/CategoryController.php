<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoriesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }

        $categories = Category::where('user_id', Auth::id())->get();
        return view('admin.categories.index' , compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }

        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoriesRequest $request)
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }

        Category::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
        ]);

        toast('Category created successfully!','success');

        return redirect()->route('admin.category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoriesRequest $request, Category $category)
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }
        $category->name = $request->name;
        $category->type = $request->type;
        $category->save();

        toast('Category updated successfully' , 'success');
        return redirect()->route('admin.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }
        $category->delete();
        toast('Category deleted successfully' , 'success');
        return redirect()->route('admin.category.index');
    }

    public function massDestroy(Request $request)
    {
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }
        Category::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
