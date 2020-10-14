<?php

namespace App\Http\Livewire\Admin\Category;

use App\Category;
use App\Formula;
use App\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $name;
    public $type = 1;
    public $category = 0;
    public $operator = 0;
    public $formula;
    public $catIds = [];

    public function updatedCategory()
    {
        $this->validate([
//            'name' => ['required'],
            'category' => ['required', Rule::notIn(0)],
        ]);

        $this->formula .= $this->category;
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

    public function store()
    {
        $this->storeValidation();
        if (!Gate::allows('categories_manage')) {
            return abort(401);
        }

        Category::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'type' => $this->type,
            'formula' => $this->formula ?? null,
        ]);



        toast('Category created successfully!', 'success');

        return redirect()->route('admin.category.index');

    }

    public function calculate()
    {

    }

    public function render()
    {
        $categories = Category::orderByDesc('id')->get();
        return view('livewire.admin.category.create', compact('categories'));
    }
}
