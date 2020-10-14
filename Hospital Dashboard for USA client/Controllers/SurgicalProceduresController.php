<?php

namespace App\Http\Controllers;

use DateTime;
use App\Metric;
use App\Patient;
use App\Patient_location;
use App\Patient_type;
use App\Patient_visit;
use App\Provider;
use App\Order;
use App\Hospital_service;

use App\Traits\FavouriteMetricsTrait;
use App\Traits\ZipCodeToCoordinates;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurgicalProceduresController extends Controller
{
	use FavouriteMetricsTrait;
	use ZipCodeToCoordinates;

	public function count(Request $request){
	    $this->registerFavouriteMetric($request);
	    
	    $data = [
	        'count' => $this->surgicalProcedures(),
	        'title' => $this->getTitle()
	    ];
	    
	    return json_encode($data);
	}
	
	public function surgicalProceduresOrderProvider(){
	    $record = Order::from(Order::getTableName().' as o')
    	    ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
    		->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
    		->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
    		->join(Provider::getTableName().' as p','o.provider_id','=','p.id')
    		->select(DB::raw('p.name_prefix,
                            p.name_first,
                            p.name_last,
                            p.name_suffix,
                            COUNT(o.id)'))
    		->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
    		->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
    		->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
    		->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
    		->groupBy('p.id')
    		->get();
		
		$data = [];
		foreach ($record as $key => $value) {
		    $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
		    $data[$key] = $value->count;
		}
		
		return json_encode($data);
	}

	public function surgicalProceduresPriority(){
	    $record = Order::from(Order::getTableName().' as o')
    	    ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
    	    ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
    	    ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
        	->select(DB::raw('o.priority, COUNT(o.id)'))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
            ->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
            ->groupBy('o.priority')
            ->get();

        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->priority;
            $data[$key] = $value->count;
        }
        
		return json_encode($data);
	}

	public function surgicalProceduresHospitalService(){
	    $record = Order::from(Order::getTableName().' as o')
    	    ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
    	    ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
    	    ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
    	    ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
        	->select(DB::raw('hs.description, COUNT(o.id) '))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
            ->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
            ->groupBy('hs.id')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->description;
            $data[$key] = $value->count;
        }
        
        return json_encode($data);
	}

	public function surgicalProceduresDay(){
	    $results = Order::from(Order::getTableName().' as o')
    	    ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
    	    ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
    	    ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
    	    ->select(DB::raw(" date(date_trunc('day', o.requested_dttm)),   COUNT(o.id)"))
    	    ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
    	    ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
    	    ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
    	    ->where("o.requested_dttm" ,">=" ,Carbon::now()->subDays(30))
    	    ->groupBy('o.requested_dttm')
    	    ->orderBy('o.requested_dttm')
    	    ->get();
	    
	    
	    $dates =[];
	    $past_date = '';
	    for($i=1;$i<=30;$i++) {
	        if($past_date == ''){
	            $past_date = date('Y-m-d', strtotime('-30 days'));
	        }
	        $datetime = new DateTime($past_date);
	        $datetime->modify('+1 day');
	        $past_date = $datetime->format('Y-m-d');
	        $dates[] = $datetime->format('Y-m-d');
	    }
	    
	    $data = [];
	    foreach ($dates as $value) {
	        $flag = false;
	        foreach ($results as $result) {
	            if($value == $result->date){
	                $record = [];
	                $record['y']    = (int)$result->count;
	                $record["label"]    = date("m-d-y", strtotime($result->date));
	                $record["indexLabel"] = "". $result->count."";
	                $data[] = $record;
	                $flag = true;
	                break;
	            }
	        }
	        if( !$flag ) {
	            $record = [];
	            $record['y']    = 0;
	            $record["label"]    = date("m-d-y", strtotime($value));
	            $record["indexLabel"] = "0";
	            $data[] = $record;
	        }
	        
	    }
	    return json_encode($data);
	}
	
	public function surgicalProceduresWeek(){
	    
	    $results = Order::from(Order::getTableName().' as o')
    	    ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
    	    ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
    	    ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
    	    ->select(DB::raw(" date(date_trunc('week', o.requested_dttm)), COUNT(o.id)"))
    	    ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
    	    ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
    	    ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
    	    ->where("o.requested_dttm" ,">=" ,Carbon::now()->subDays(90))
    	    ->groupBy(DB::raw(" date(date_trunc('week', o.requested_dttm))"))
    	    ->orderBy(DB::raw(" date(date_trunc('week', o.requested_dttm))"))
    	    ->get();


		$weekdates = [];
		for($i=1; $i<=14; $i++){
			$start = Carbon::now()->startOfWeek()->subWeeks( 14 - $i);
			$end = $start->copy()->endOfWeek();
			$weekdates[] = $start->format('m-d-y') . ' to ' .  $end->format('m-d-y');
		}
	    
	    $model_data = [];
	    foreach ($results as $value) {
	        $record = [];
	        $record['y']    = (int)$value->count;
	        $record["label"]    = $this->getStartAndEndDate($value->date);
	        $record["indexLabel"] = "".$value->count."";
	        $model_data[] = $record;
	    }
	    
	    $actual_data = [];
	    foreach ($weekdates as $date) {
	        $flag = false;
	        foreach ($model_data as $data) {
	            if($date == $data["label"]){
	                $record=[];
	                $record['y']=$data['y'];
	                $record["label"]= $data["label"];
	                $record["indexLabel"]=$data["indexLabel"];
	                $actual_data[]=$record;
	                $flag = true;
	                break;
	            }
	        }
	        if( !$flag ) {
	            $record=[];
	            $record['y']= 0;
	            $record["label"]= $date;
	            $record["indexLabel"]="0";
	            $actual_data[]=$record;
	        }
	    }
	    return json_encode($actual_data);
	    
	}
	
	public function surgicalProceduresMonth(){
	    $results = Order::from(Order::getTableName().' as o')
    	    ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
    	    ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
    	    ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
    	    ->select(DB::raw(" date(date_trunc('month', o.requested_dttm)), COUNT(o.id)"))
    	    ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
    	    ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
    	    ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
    	    ->where("o.requested_dttm" ,">=" ,Carbon::now()->subDays(365))
    	    ->groupBy(DB::raw(" date(date_trunc('month', o.requested_dttm))"))
    	    ->orderBy(DB::raw(" date(date_trunc('month', o.requested_dttm))"))
    	    ->get();
	   
	    
	    $months = [];
	    $past_date = '';
	    for($i=1;$i<=12;$i++) {
	        if($past_date == ''){
	            $past_date = date('Y-m-d', strtotime('-365 days'));
	        }
	        $datetime = new DateTime($past_date);
	        $datetime->modify('+1 month');
	        $past_date = $datetime->format('F-Y');
	        $months[] = $datetime->format('F-Y');
	    }
	    $data = [];
	    foreach ($months as $value) {
	        $flag = false;
	        foreach ($results as $result) {
	            $record = [];
	            if($value === date("F-Y", strtotime($result->date))){
	                
	                $record['y']    = (int)$result->count;
	                $record["label"]    = date("F-Y", strtotime($result->date));
	                $record["indexLabel"] =  "".$result->count."";
	                $data[] = $record;
	                $flag = true;
	                break;
	            }
	        }
	        if( !$flag ) {
	            $record = [];
	            $record['y']    = 0;
	            $record["label"]    = $value;
	            $record["indexLabel"] = "0";
	            $data[] = $record;
	        }
	    }
	    return json_encode($data);
	}

	public function surgicalProceduresweekFromtoEnd(Request $request){
		$date = explode('to ', $request->input('FromToDate'));
		$start = explode('-',$date[0]);
		$end  = explode('-',$date[1]);

		$startdateTime = new DateTime($start[1].'-'.$start[0].'-'.$start[2]);
		$start_date = $startdateTime->format('d-m-y');
		$enddateTime =  new DateTime($end[1].'-'.$end[0].'-'.$end[2]);
		$end_date  =  $enddateTime->format('d-m-y')." 23:59:59" ;

		$from =  date('Y-m-d H:i:s', strtotime($start_date));
		$to = date('Y-m-d H:i:s', strtotime($end_date));

		$results = Order::from(Order::getTableName().' as o')
			->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
			->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
			->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
			->select(DB::raw(" date(date_trunc('day', o.requested_dttm)),   COUNT(o.id)"))
			->whereBetween('o.requested_dttm', [$from , $to]) 
			->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
			->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
			->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
			->groupBy('o.requested_dttm')
			->orderBy('o.requested_dttm')
			->get();

		$dates =[];
		$past_date = '';

		for($i=1;$i<=6;$i++) {
			if($past_date == ''){
				$past_date = date('Y-m-d', strtotime($from));
				$dates[] = date('Y-m-d', strtotime($from));
			}
			$datetime = new DateTime($past_date);
			$datetime->modify('+1 day');
			$past_date = $datetime->format('Y-m-d');
			$dates[] = $datetime->format('Y-m-d');
		}

		$data = [];
		foreach ($dates as $value) {
			$flag = false;
			foreach ($results as $result) {
				if($value == $result->date){
					$record = [];
					$record['y']    = (int)$result->count;
					$record["label"]    = date("m-d-y", strtotime($result->date));
					$record["indexLabel"] = "". $result->count."";
					$data[] = $record;
					$flag = true;
					break;
				}
			}
			if( !$flag ) {
				$record = [];
				$record['y']    = 0;
				$record["label"]    = date("m-d-y", strtotime($value));
				$record["indexLabel"] = "0";
				$data[] = $record;
			}

		}
		return json_encode($data);

	}

	public function surgicalProceduresMonthYear(Request $request){

		$MonthYear =  new DateTime($request->input('MonthYear'));
		$MonthYearDate  =  $MonthYear->format('y-m-d');
		$from =  date('Y-m-d H:i:s', strtotime($MonthYearDate));
		$to   = date('Y-m-t H:i:s', strtotime($MonthYearDate));
		
		$results = Order::from(Order::getTableName().' as o')
			->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
			->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
			->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
			->select(DB::raw(" date(date_trunc('week', o.requested_dttm)), COUNT(o.id)"))
			->whereBetween('o.requested_dttm', [$from , $to]) 
			->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
			->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
			->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
			->groupBy(DB::raw(" date(date_trunc('week', o.requested_dttm))"))
			->orderBy(DB::raw(" date(date_trunc('week', o.requested_dttm))"))
			->get();


		$signupdate= $from;
		$signupweek=date("W",strtotime($signupdate));
		$year=date("Y",strtotime($signupdate));
		$currentweek = date("W",strtotime($to));
		$weekdates = [];
        
		if( $signupweek > $currentweek){
		    $currentweek = $signupweek + 4;
		}
		for($i=$signupweek;$i<=$currentweek;$i++) {
			$weekdates[]= $this->getWeek($i,$year);
		}
        
		$model_data = [];
		foreach ($results as $value) {
			$record = [];
			$record['y']    = (int)$value->count;
			$record["label"]    = $this->getStartAndEndDate($value->date);
			$record["indexLabel"] = "".$value->count."";
			$model_data[] = $record;
		}

		$actual_data = [];
		foreach ($weekdates as $date) {
			$flag = false;
			foreach ($model_data as $data) {
				if($date == $data["label"]){
					$record=[];
					$record['y']=$data['y'];
					$record["label"]= $data["label"];
					$record["indexLabel"]=$data["indexLabel"];
					$actual_data[]=$record;
					$flag = true;
					break;
				}
			}
			if( !$flag ) {
				$record=[];
				$record['y']= 0;
				$record["label"]= $date;
				$record["indexLabel"]="0";
				$actual_data[]=$record;
			}
		}
		return json_encode($actual_data);


	}
	
	public function getHelper(){
		//Taking metric details from Metric
		$record = Metric::where('uid','=',Metric::$TYPE_SP)->first();

		$data = [
			'desc'   => $record['metric_description'],
			'cal'   => $record['metric_help'],

		];

		return json_encode($data);
	}

	public function getMapData(){
	    $record = Order::from(Order::getTableName().' as o')
            ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
			->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
			->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
			->join(Patient::getTableName() . ' as p','p.id','=','pv.patientid')
			->select('p.address_zipcode')
			->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
			->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
    	    ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
    	    ->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
			->get('address_zipcode')->toArray();

		$record = array_filter($record, function ($zipCode) {
			return $zipCode['address_zipcode']!== null;
		});

		$coordinates = array_map(function($zipCode) {
			return self::getCoordinatesByZipCode($zipCode['address_zipcode']);
		}, $record);

		$coordinates = array_values($coordinates);
		
		return json_encode($coordinates);
	}

	protected function getTitle(){
	    $record = Metric::from(Metric::getTableName() .' as m')
	    ->select('m.metric_name')
	    ->where('m.uid', '=', Metric::$TYPE_SP )
	    ->first();
	    
	    return $record->metric_name;
	}
	
	protected function getStartAndEndDate($date) {
	    $dateTime = new DateTime($date);
	    $start_date = $dateTime->format('m-d-y');
	    $dateTime->modify('+6 days');
	    $end_date = $dateTime->format('m-d-y');
	    return $start_date.' to ' .$end_date;
	}
	
	protected function getWeek($week, $year) {
	    $dto = new DateTime();
	    $start_date = $dto->setISODate($year, $week , 1)->format('m-d-y');
	    $end_date = $dto->setISODate($year, $week, 7)->format('m-d-y');
	    return $start_date.' to ' .$end_date;
	}
}
