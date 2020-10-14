<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Metric;
use App\Metric_favorite;
use Session;

use App\Traits\FavouriteMetricsTrait;

class DashboardController extends Controller
{
    use FavouriteMetricsTrait;
    public function index()
    {
        $metric_register = $this->getMenuFavourite();
        $metric_count = $this->getMetricCount();
        $metric_title = $this->getMetricTitles();
	    $metric_color_order = $this->getMetricColorAndOrder();
	    $metric_color = $metric_color_order[0];
	    $metric_order = $metric_color_order[1];
        $user_config_id = $this->getUserConfigId();
        return view('dashboard')->with(compact('metric_register', 'metric_count', 'metric_title', 'metric_color', 'user_config_id' , 'metric_order'));
    }

    public function favouriteMenu(){
        Session::put('favourite', 1);
        $user = Auth::user();
        $menu_data = Metric::from(Metric::getTableName().' as m')
        ->join(Metric_favorite::getTableName().' as mf','mf.metricid','=','m.id')
        ->select('m.uid')
        ->where('mf.userid','=',$user->id)->get();
        
        $menu = array(
            'fav' => []
        );
        
        foreach ($menu_data as $value) {
            $menu['fav'][] = $value->uid;
        }
        return json_encode($menu);
    }
}
