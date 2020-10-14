<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Payment;
use App\Report;
use App\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public $data = [];
    public $rest = [];
    public $totalIncomingAmount = [];
    public $totalOutgoingAmount = [];


    public function index()
    {
        return view('admin.reports.index');
    }

    public function daily()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())->get();
        return view('admin.reports.daily', compact('restaurants'));
    }

    public function weekly()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())->get();
        return view('admin.reports.weekly', compact('restaurants'));
    }

    public function monthly()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())->get();
        return view('admin.reports.monthly', compact('restaurants'));
    }

    public function details()
    {
        $restaurants = Restaurant::where('user_id', Auth::id())->get();
        return view('admin.reports.details', compact('restaurants'));
    }

    public function ajaxDaily(Request $request)
    {
        $restId = $request->input('restId');
        $restaurants = $this->getRestaurants($restId);

        foreach ($restaurants as $restaurant) {
            $this->rest[] = $restaurant->name;
            $this->totalIncomingAmount[] = $this->getAmount($restaurant->id, 1);
            $this->totalOutgoingAmount[] = $this->getAmount($restaurant->id, 2);
        }
        $this->data['restaurants'] = $this->rest;
        $this->data['incomingAmount'] = $this->totalIncomingAmount;
        $this->data['outgoingAmount'] = $this->totalOutgoingAmount;
        return response($this->data, 200);
    }

    public function ajaxWeekly(Request $request)
    {
        $fromDate = Carbon::today();
        $tillDate = Carbon::today()->subWeek();

        $restId = $request->input('restId');
        $restaurants = $this->getRestaurants($restId);
        foreach ($restaurants as $restaurant) {
            $this->rest[] = $restaurant->name;
            $this->totalIncomingAmount[] = $this->getAmountBetween($restaurant->id, 1, $fromDate, $tillDate);
            $this->totalOutgoingAmount[] = $this->getAmountBetween($restaurant->id, 2, $fromDate, $tillDate);
        }
        $this->data['restaurants'] = $this->rest;
        $this->data['incomingAmount'] = $this->totalIncomingAmount;
        $this->data['outgoingAmount'] = $this->totalOutgoingAmount;
        return response($this->data, 200);
    }

    public function ajaxMonthly(Request $request)
    {
        $fromDate = Carbon::today();
        $tillDate = Carbon::today()->subMonth();

        $restId = $request->input('restId');
        $restaurants = $this->getRestaurants($restId);
        foreach ($restaurants as $restaurant) {
            $this->rest[] = $restaurant->name;
            $this->totalIncomingAmount[] = $this->getAmountBetween($restaurant->id, 1, $fromDate, $tillDate);
            $this->totalOutgoingAmount[] = $this->getAmountBetween($restaurant->id, 2, $fromDate, $tillDate);
        }
        $this->data['restaurants'] = $this->rest;
        $this->data['incomingAmount'] = $this->totalIncomingAmount;
        $this->data['outgoingAmount'] = $this->totalOutgoingAmount;
        return response($this->data, 200);
    }

    public function ajaxDetails(Request $request)
    {
        $from = \request('from');
        $to = \request('to');

        $fromDate = Carbon::parse($from);
        $tillDate = Carbon::parse($to);

        $restId = $request->input('restId');
        $restaurants = $this->getRestaurants($restId);
        foreach ($restaurants as $restaurant) {
            $this->rest[] = $restaurant->name;
            $this->totalIncomingAmount[] = $this->getAmountBetween($restaurant->id, 1, $fromDate, $tillDate);
            $this->totalOutgoingAmount[] = $this->getAmountBetween($restaurant->id, 2, $fromDate, $tillDate);
        }
        $this->data['restaurants'] = $this->rest;
        $this->data['incomingAmount'] = $this->totalIncomingAmount;
        $this->data['outgoingAmount'] = $this->totalOutgoingAmount;
        return response($this->data, 200);
    }

    public function getAmount($restaurantId, $type)
    {
        return Payment::with('category')
            ->where('restaurant_id', $restaurantId)
            ->whereDate('created_at', Carbon::today())
            ->whereHas('category', static function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->get()
            ->sum('amount');
    }

    public function getAmountBetween($restaurantId, $type, $start, $end)
    {
        return Payment::with('category')
            ->where('restaurant_id', $restaurantId)
            ->whereDate('created_at', '<=', $start)
            ->whereDate('created_at', '>=', $end)
            ->whereHas('category', static function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->get()
            ->sum('amount');
    }

    public function getRestaurants($id)
    {
        if ($id != 0) {
            return Restaurant::where('user_id', Auth::id())->where('id', $id)->get();
        }
        return Restaurant::where('user_id', Auth::id())->get();
    }

}
