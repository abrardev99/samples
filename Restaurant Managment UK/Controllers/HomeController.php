<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())->get();
        return view('home', compact('restaurants'));
    }
}
