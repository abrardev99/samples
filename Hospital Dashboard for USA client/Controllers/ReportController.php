<?php

namespace App\Http\Controllers;

use Session;
use App\Traits\FavouriteMetricsTrait;

class ReportController extends Controller
{
    use FavouriteMetricsTrait;
    public function index()
    {
        $trend_register = $this->getMenuFavourite();
        $trend_title = $this->getMetricTitles();
        $metric_color_order = $this->getMetricColorAndOrder();
        $metric_order = $metric_color_order[1];

        return view('reports')->with(compact('trend_register', 'trend_title', 'metric_order'));
    }
}
