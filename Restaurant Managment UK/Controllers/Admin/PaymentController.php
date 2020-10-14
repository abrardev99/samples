<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Payment;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        $payments = Payment::with(['restaurant' ,'category'])->where('user_id', Auth::id())->get();
        return view('admin.payments.index' , compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        $restaurants = Restaurant::all();
        $categories = Category::all();
        return view('admin.payments.create', compact('restaurants', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentRequest $request)
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        Payment::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $request->restaurant,
            'category_id' => $request->category,
            'amount' => $request->amount,
        ]);

        toast('Payment created successfully', 'success');
        return redirect()->route('admin.payment.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        $restaurants = Restaurant::all();
        $categories = Category::all();
        return view('admin.payments.edit', compact('payment', 'restaurants', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(StorePaymentRequest $request, Payment $payment)
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        $payment->restaurant_id = $request->restaurant;
        $payment->category_id = $request->category;
        $payment->amount = $request->amount;
        $payment->save();

        toast('Payment updated successfully', 'success');
        return redirect()->route('admin.payment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }

        $payment->delete();
        toast('Payment deleted successfully' , 'success');
        return redirect()->route('admin.payment.index');
    }

    public function massDestroy(Request $request)
    {
        if (! Gate::allows('payments_manage')) {
            return abort(401);
        }
        Payment::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
