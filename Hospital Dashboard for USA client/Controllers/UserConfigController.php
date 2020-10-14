<?php

namespace App\Http\Controllers;

use App\Metric;
use App\Metric_config;
use App\User_config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserConfigController extends Controller
{
    public function saveUserConfig(Request $request){
        $userConfig = User_config::where('id', '=', $request->id)->first();
	       
	    if($userConfig){
            
		    $userConfig->threshold1 = $request->threshold1;
		    $userConfig->threshold2 = $request->threshold2;
		    $userConfig->timestamps = false;
		    $userConfig->save();
		    
		    return response ()->json($userConfig);
	    }
	
    }

    public function getUserConfig(){

    	$id = $_GET['id'];
    	$user_config = User_config::findOrFail($id);
        
    	$metric_config = [];
    	if($user_config){
        	$metric_id = $user_config->metricid;
    	    $metric_config = Metric_config::from(Metric_config::getTableName() .' as mc')
    		    ->join(Metric::getTableName() . ' as m', 'm.configid','=','mc.id')
    		    ->select('mc.*')
    		    ->where('m.id', '=', $metric_id)
    		    ->get()->toArray();
    	}
    	
        $data =  array('user_config'=>$user_config, 'metric_config'=>$metric_config);
	    return json_encode($data);
    }
}
