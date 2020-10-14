<?php

namespace App\Http\Livewire\Admin\Category;

use App\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Edit extends Component
{
    public $categoryId;
    public $name;
    public $type = 1;
    public $category = 0;
    public $formula;
    public function mount(Category $category)
    {
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type;
        $this->formula = $category->formula;
    }

    public function storeValidation()
    {
        $rules = [
            'name' => ['required'],
            'type' => ['required' , 'numeric'],
        ];
        if($this->type === 3){
            $rules['formula'] = ['required'];
        }
        $this->validate($rules);
    }

    public function update()
    {
        $this->storeValidation();
        if (! Gate::allows('categories_manage')) {
            return abort(401);
        }

        $category = Category::findOrFail($this->categoryId);
        $category->user_id = Auth::id();
        $category->name = $this->name;
        $category->type = $this->type;
        $category->formula = $this->formula ?? null;
        $category->save();

        toast('Category updated successfully!','success');

        return redirect()->route('admin.category.index');

    }

    public function render()
    {
        $categories = Category::orderByDesc('id')->get();
        return view('livewire.admin.category.edit', compact('categories'));
    }
}
