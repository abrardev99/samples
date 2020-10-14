<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Session;
use App\Traits\FavouriteMetricsTrait;

class HomeController extends Controller
{
    use FavouriteMetricsTrait;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Set metric/trend favorite and user config for new user to default if it don't exist
        $this->setMetricDefaultForNewUser();
        
        $metric_register = $this->getMenuFavourite();
        $metric_count = $this->getMetricCount();
    	$metric_title = $this->getMetricTitles();
    	$metric_color_order = $this->getMetricColorAndOrder();
	    $metric_color = $metric_color_order[0];
	    $metric_order = $metric_color_order[1];
    	$user_config_id = $this->getUserConfigId();

	    if(!Auth::user()->changed_password){
	       return view('auth.passwords.change_password');
	    }
	    return view('home')->with(compact('metric_register', 'metric_count', 'metric_title', 'metric_color', 'user_config_id' , 'metric_order'));
    }

    public function sessionUpdate(){
        Session::put('favourite',0);
        Session::put('trend_favourite',0);
    }
}

