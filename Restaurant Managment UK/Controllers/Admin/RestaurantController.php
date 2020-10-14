<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRestuarantRequest;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }
        $restaurants = Restaurant::where('user_id', Auth::id())->get();
        return view('admin.restaurants.index' , compact('restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }

        return view('admin.restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRestuarantRequest $request)
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }

        Restaurant::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        toast('Restaurant created successfully' , 'success');
        return redirect()->route('admin.restaurant.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }

        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }

        $categories = Category::orderByDesc('id')->get();
        return view('admin.restaurants.edit' , compact('restaurant' , 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRestuarantRequest $request, Restaurant $restaurant)
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }

        $restaurant->name = $request->name;
        $restaurant->phone = $request->phone;
        $restaurant->address = $request->address;
        $restaurant->save();

        toast('Restaurant updated successfully');
        return redirect()->route('admin.restaurant.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }

        $restaurant->delete();
        toast('Restaurant deleted successfully' , 'success');
        return redirect()->route('admin.restaurant.index');
    }

    public function massDestroy(Request $request)
    {
        if (! Gate::allows('restaurants_manage')) {
            return abort(401);
        }
        Restaurant::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}
