<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

use Session;
use App\Metric;
use App\Metric_favorite;
use App\Metric_config;
use App\User;

use App\Traits\FavouriteMetricsTrait;
use App\User_config;

class MetricsController extends Controller
{
    use FavouriteMetricsTrait;
    public function index()
    {    
        $metric_register = $this->getMenuFavourite();
        $metric_title = $this->getMetricTitles();
        $metric_color_order = $this->getMetricColorAndOrder();
        $metric_order = $metric_color_order[1];

        return view('metrics')->with(compact('metric_register', 'metric_title', 'metric_order'));
    }
    
    public function getMetric()
    {
        $user = Auth::user();
        if(!$user->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $data = DB::table(Metric::getTableName().' as m')
        ->join(Metric_config::getTableName().' as mc','m.configid','=','mc.id')
        ->select('m.*', 'mc.high', 'mc.low')
        ->get();
        
        return datatables()->of($data)
        ->addColumn('action', 'metric_action_button')
        ->rawColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if(!$user->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $rules = [];
        if( !isset($request->metric_id) ){
            // new user
            $rules = Metric::rules();
        } else {
            $rules = Metric::rules($request->metric_id);
        }
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response ()->json($validator->errors(), 422);
        }
        
        $configId = $request->configid;
        
        $metric_config =  Metric_config::updateOrCreate(
            ['id' => $configId],
            [
                'high' => $request->high,
                'low' => $request->low,
            ]);
        
        
        $metricId = $request->metric_id;
        $metric =  Metric::updateOrCreate(
            ['id' => $metricId],
            [
                'metric_name' => $request->metric_name,
                'metric_description' => $request->metric_description,
                'metric_help' => $request->metric_help,
                'uid' => $request->uid,
                'configid' => $metric_config->id
            ]);
        
        
        // update user config table when add new metric
        if( !isset($metricId) ){
            $users = User::get();
            
            $threshold1 = intdiv($request->high, 2);
            $threshold2 = intdiv($threshold1, 2);
            foreach ($users as $user) {
                User_config::updateOrCreate(
                    ['id' => null],
                    [
                        'userid' => $user->id,
                        'metricid' => $metric->id,
                        'threshold1' => $threshold1,
                        'threshold2' => $threshold2
                    ]);
            }
        }
        
        return response ()->json($metric);
    }
    
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        if(!$user->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $metric = DB::table(Metric::getTableName().' as m')
        ->join(Metric_config::getTableName().' as mc','m.configid','=','mc.id')
        ->select('m.*', 'mc.low', 'mc.high')
        ->where("m.id" ,"=" ,$id)
        ->first();
        
        return response ()->json($metric);
    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if(!$user->isAdministrator()){
            return response ()->json("Error: User isn't administrator", 405);
        }
        
        $record = DB::table(Metric::getTableName().' as m')
        ->join(Metric_config::getTableName().' as mc','m.configid','=','mc.id')
        ->where("m.id" ,"=" ,$id)
        ->first();
        
        $metric = Metric::where('id',$id)->delete();
        if( isset($record->configid) ){
            Metric_config::where('id',$record->configid)->delete();
        }
        
        return response ()->json($metric);
    }
}
