<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;
use DateTime;
use Carbon\Carbon;

use App\Metric;
use App\Patient_location;
use App\Patient_type;
use App\Patient_visit;

use App\Traits\FavouriteMetricsTrait;


class BedutilizationmetricController extends Controller
{
    use FavouriteMetricsTrait;
    public function count(Request $request){
        
        $this->registerFavouriteMetric($request);

        $data = [
            'count' => $this->bedUtilization2(),
            'title' => $this->getTitle()
        ];
        return json_encode($data);
    }

    public function bedutilizationNursingUnit(){
        $totalBeds = Patient_location::from(Patient_location::getTableName(). ' as pl')
            ->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->select(DB::raw('pl.id, pl.numofbeds, pl.location'))
            ->get();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_type::getTableName() . ' as pt', function($join){
                $join->on('pt.id','=','pl.patienttypeid')->on('pt.id','=','pv.patienttype'); })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->whereNull("pv.dischargedate")
            ->whereNotNull("pv.admitdate")
            ->select(DB::raw('pl.id, pl.numofbeds, pl.location, COUNT(pv.id)'))
            ->groupBy('pl.id')
            ->get();
        
        $data = [];
        foreach ($totalBeds as $beds ) {
            $flag = false;
            foreach ($record as $value) {
                if( $beds->id == $value->id ){
                    $cal = 0;
                    if( $value->numofbeds > 0 ){
                        $cal = ($value->count / $value->numofbeds) * 100;
                    }
                    
                    if( $cal > 100 ){
                        $cal = 100;
                    }
                    
                    $data[$value->location] = round($cal). '%';
                    $flag = true;
                    break;
                }
            }
            if( !$flag ){
                $data[$beds->location] ='0%';
            }
        }
                
        return json_encode($data);
    }

    public function bedutilizationEmptyBeds(){
        $totalBeds = Patient_location::from(Patient_location::getTableName(). ' as pl')
            ->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->select(DB::raw('pl.id, pl.numofbeds, pl.location'))
            ->get();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_type::getTableName() . ' as pt', function($join){
                $join->on('pt.id','=','pl.patienttypeid')->on('pt.id','=','pv.patienttype'); })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->whereNull("pv.dischargedate")
            ->whereNotNull("pv.admitdate")
            ->select(DB::raw('pl.id, pl.numofbeds, pl.location, COUNT(pv.id)'))
            ->groupBy('pl.id')
            ->get();
        
        $data = [];
        foreach ($totalBeds as $beds ) {
            $flag = false;
            foreach ($record as $value) {
                if( $beds->id == $value->id ){
                    $cal = $value->numofbeds - $value->count;
                    if( $cal <= 0 ){
                        $cal = 0;
                    } else if( $cal > $value->numofbeds ){
                        $cal = $value->numofbeds;
                    }
                    
                    $data[$value->location] = $cal;
                    $flag = true;
                    break;
                }
            }
            if( !$flag ){
                $data[$beds->location] = $beds->numofbeds;
            }
        }
        
        return json_encode($data);
    }

    public function bedutilizationDay(){
        $total = Patient_location::from(Patient_location::getTableName(). ' as pl')
            ->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->sum('numofbeds');
        
        $results = [];
        for($i=1;$i<=30;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfDay()->subDays(30 - $i);
            $endTime = $beginTime->copy()->endOfDay();
            
            $results[] = $this->queryInpatientBeds($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $totalBeds = $total * $value->diffInDays;
            $cal = 0;
            if($totalBeds > 0){
                $cal = round(($value->inpatientbeds / $totalBeds ) * 100);
            }
            if( $cal > 100 ){
                $cal = 100;
            }
            
            $record = [];
            $record['y']    = $cal;
            $record["label"]    = date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$cal."%";
            $data[] = $record;
        }
            
        return json_encode($data);
    }

    public function bedutilizationWeek(){
        
        $total = Patient_location::from(Patient_location::getTableName(). ' as pl')
            ->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->sum('numofbeds');
        
        $results = [];
        for($i=1;$i<=14;$i++) {    
            // query data from database
            $beginTime = Carbon::now()->startOfWeek()->subWeeks( 14 - $i);
            $endTime = $beginTime->copy()->endOfWeek();
            
            $results[] = $this->queryInpatientBeds($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $totalBeds = $total * $value->diffInDays;
            $cal = 0;
            if($totalBeds > 0){
                $cal = round(($value->inpatientbeds / $totalBeds ) * 100);
            }
            if( $cal > 100 ){
                $cal = 100;
            }
            
            $record = [];
            $record['y']    = $cal;
            $record["label"]    = date("m-d-Y", strtotime($value->begindate)). " to ". date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$cal."%";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    public function bedutilizationMonth(){ 
        $total = Patient_location::from(Patient_location::getTableName(). ' as pl')
            ->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->sum('numofbeds');
        
        $results = [];
        for($i=1;$i<=12;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfMonth()->subMonths( 12 - $i);
            if( $i == 12 ){
                $endTime = Carbon::now()->endOfDay();
            } else {
                $endTime = $beginTime->copy()->endOfMonth();
            }
            
            $results[] = $this->queryInpatientBeds($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $totalBeds = $total * $value->diffInDays;
            $cal = 0;
            if($totalBeds > 0){
                $cal = round(($value->inpatientbeds / $totalBeds ) * 100);
            }
            if( $cal > 100 ){
                $cal = 100;
            }
            
            $record = [];
            $record['y']    = $cal;
            $record["label"]    = date("F-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$cal."%";
            $data[] = $record;
        }
        return json_encode($data);
    }
    
    protected function queryInpatientBeds( $beginTime, $endTime ){
        $now = Carbon::now();
        $inPatientBeds = 0;
        $beginDate = $beginTime->toDateString();
        $endDate = $endTime->toDateString();
        
        if( $beginTime <= $now ){
            if( $endTime > $now->endOfDay() ){
                $endTime = $now;
            }
            $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
                ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
                ->join(Patient_type::getTableName() . ' as pt', function($join){
                    $join->on('pt.id','=','pl.patienttypeid')->on('pt.id','=','pv.patienttype'); })
                ->select(DB::raw("SUM (CASE
                                        WHEN pv.dischargedate IS NOT NULL THEN
                                            CASE
                                        	   WHEN pv.admitdate >= '$beginTime' AND pv.dischargedate > '$endTime' THEN
                                        		  DATE_PART('day', timestamp '$endTime' - pv.admitdate) + 1
                                        	   WHEN pv.admitdate >= '$beginTime' AND pv.dischargedate <= '$endTime' THEN
                                            DATE_PART('day', pv.dischargedate - pv.admitdate) + 1
                                        	   WHEN pv.admitdate < '$beginTime' AND pv.dischargedate > '$endTime' THEN
                                            DATE_PART('day', timestamp '$endTime' - timestamp '$beginTime') + 1
                                        	   WHEN pv.admitdate < '$beginTime' AND pv.dischargedate <= '$endTime' THEN
                                        		  DATE_PART('day', pv.dischargedate - timestamp '$beginTime') + 1
                                        ELSE
                                        		  0
                                        	END
                                        ELSE
                                        	CASE
                                              WHEN pv.admitdate >= '$beginTime' THEN
                                            DATE_PART('day', timestamp '$endTime' - pv.admitdate ) + 1
                                        	  WHEN pv.admitdate < '$beginTime' THEN
                                        		  DATE_PART('day', timestamp '$endTime' - timestamp '$beginTime') + 1
                                        	  ELSE
                                        		  0
                                        	END	
                                        END)
                                   as inpatientbeds"))
                ->whereNotNull('pv.admitdate')
                ->where('pv.admitdate', '<=', $endTime)
                ->where(function ($query) use ($beginTime){
                    $query->whereNull('pv.dischargedate')
                    ->orWhere('pv.dischargedate', '>=', $beginTime);
                })
                ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
                ->where("pl.code","!=",Patient_location::$TYPE_ER)
                ->first();
            
            $inPatientBeds = (int)$results->inpatientbeds;
        }
        
        $data = (object)[
            'diffInDays' => $endTime->diffInDays($beginTime) + 1,
            'begindate' => $beginDate,
            'enddate' => $endDate,
            'inpatientbeds' => $inPatientBeds
        ];
            
        return $data;
    }

    public function bedUtilizationWeekFromtoEnd(Request $request){
	    $date = explode('to ', $request->input('FromToDate'));
	    $start = explode('-',$date[0]);
	    $end  = explode('-',$date[1]);

	    $from =  Carbon::parse($start[1].'-'.$start[0].'-'.$start[2]);
	    $to = Carbon::parse($end[1].'-'.$end[0].'-'.$end[2]);

	    $total = Patient_location::from(Patient_location::getTableName(). ' as pl')
		    ->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
		    ->where("pt.code", "=", Patient_type::$TYPE_I)
		    ->sum('numofbeds');

	    $days = $from->diffInDays($to) + 1;
	    
	    $results = [];
	    for($i=1;$i<=$days;$i++) {
		    // query data from database
	        $beginTime = $from->copy()->startOfDay()->addDays($i-1);
	        $endTime = $beginTime->copy()->endOfDay();
		    $results[] = $this->queryInpatientBeds($beginTime, $endTime);
	    }
	    
	    $data = [];
	    foreach ($results as $value) {
		    $totalBeds = $total * $value->diffInDays;
		    $cal = 0;
		    if($totalBeds > 0){
			    $cal = round(($value->inpatientbeds / $totalBeds ) * 100);
		    }
		    if( $cal > 100 ){
			    $cal = 100;
		    }

		    $record = [];
		    $record['y']    = $cal;
		    $record["label"]    = date("m-d-Y", strtotime($value->enddate));
		    $record["indexLabel"] = "".$cal."%";
		    $data[] = $record;
	    }


	    return json_encode($data);
	}

	public function bedUtilizationMonthYear(Request $request){

		$MonthYear =  new DateTime($request->input('MonthYear'));
		$MonthYearDate  =  $MonthYear->format('y-m-d');
		$from =  date('Y-m-d H:i:s', strtotime($MonthYearDate));
		$to   = date('Y-m-t H:i:s', strtotime($MonthYearDate));
		$numOfWeeks = Carbon::parse($to)->diffInWeeks(Carbon::parse($from)) + 1;
		
		$total = Patient_location::from(Patient_location::getTableName(). ' as pl')
			->join(Patient_type::getTableName() . ' as pt','pl.patienttypeid','=','pt.id')
			->where("pt.code", "=", Patient_type::$TYPE_I)
			->sum('numofbeds');

		$results = [];
		$beginDate = Carbon::parse($from)->startOfWeek();
		$endDate = $beginDate->copy()->endOfWeek();
		for($i=1;$i<=$numOfWeeks;$i++) {
			// query data from database
			if($i > 1){
				$beginDate->addWeek(1);
				$endDate->addWeek(1);
			}

			$results[] = $this->queryInpatientBeds($beginDate, $endDate);
		}

		$data = [];
		foreach ($results as $value) {
			$totalBeds = $total * $value->diffInDays;
			$cal = 0;
			if($totalBeds > 0){
				$cal = round(($value->inpatientbeds / $totalBeds ) * 100);
			}
			if( $cal > 100 ){
				$cal = 100;
			}

			$record = [];
			$record['y']    = $cal;
			$record["label"]    = date("m-d-Y", strtotime($value->begindate)). " to ". date("m-d-Y", strtotime($value->enddate));
			$record["indexLabel"] = "".$cal."%";
			$data[] = $record;
		}

		return json_encode($data);
	}
    
    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_BU)->first();
        
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_BU )
        ->first();
        
        return $record->metric_name;
    }
}
