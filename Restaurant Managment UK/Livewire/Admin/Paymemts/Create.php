<?php

namespace App\Http\Livewire\Admin\Paymemts;

use App\Category;
use App\Payment;
use App\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public $restaurant = 0;
    public $category = 0;
    public $amount;
    public $purchase;
    public $sale;
    public $formula = null;
    public $result;
    public $payment_date;

    public function updatedCategory()
    {
        $category = Category::findOrFail($this->category);
        if ($category->type == 3){
            $this->formula = $category->formula;
        }
    }

    public function calculate()
    {
        try {
            $exp = $this->formula;
            $this->amount = eval("return $exp;");
        }
        catch (\Exception $e){

    }
    }
    
    public function save()
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        $this->validate([
            'restaurant' => ['required' , Rule::notIn(0)],
            'category' => ['required' , Rule::notIn(0)],
            'amount' => ['required', 'numeric'],
        ]);

        Payment::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $this->restaurant,
            'category_id' => $this->category,
            'amount' => $this->amount,
        ]);

        toast('Payment created successfully', 'success');
        return redirect()->route('admin.payment.index');
    }
    public function render()
    {
        $restaurants = Restaurant::all();
        $categories = Category::all();
        return view('livewire.admin.paymemts.create', compact('restaurants', 'categories'));
    }
}
