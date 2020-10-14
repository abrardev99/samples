<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use DateTime;

use App\Metric;
use App\Patient_visit;
use App\Patient_type;
use App\Patient_location;
use App\Provider;
use App\Hospital_service;

use App\Traits\FavouriteMetricsTrait;
use Carbon\Carbon;


class PatientdaysmetricController extends Controller
{
    use FavouriteMetricsTrait;
    public function count(Request $request){

        $this->registerFavouriteMetric($request);

        $data = [
            'count' => $this->patientDays(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }

    public function patientDaysNursingUnit(){
        $beginTime = Carbon::now()->startOfDay();
        $endTime = Carbon::now();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select('pl.location', 
                        DB::raw("SUM (CASE
                                        WHEN pv.admitdate > '$beginTime' THEN 0
                                        ELSE 1
                                     END)
                               as totalpatientdays")
                    )
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('pl.id')
            ->get();
                
        $data = [];
        foreach ($record as $value) {
            $data[$value->location] = $value->totalpatientdays;
        }
        return json_encode($data);
    }

    public function patientDaysHospitalService(){
        $beginTime = Carbon::now()->startOfDay();
        $endTime = Carbon::now();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
        ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
        ->select('hs.description', 
                    DB::raw("SUM (CASE
                                    WHEN pv.admitdate > '$beginTime' THEN 0
                                    ELSE 1
                                 END)
                               as totalpatientdays")
                )
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('hs.id')
            ->get();
        
        $data = [];
        foreach ($record as $value) {
            $data[$value->description] = $value->totalpatientdays;
        }
        return json_encode($data);
    }

    public function patientDaysReferringProvider(){
        
        $beginTime = Carbon::now()->startOfDay();
        $endTime = Carbon::now();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
            ->select('p.name_first', 'p.name_last', 'p.name_suffix',
                        DB::raw("SUM (CASE
                                        WHEN pv.admitdate > '$beginTime' THEN 0
                                        ELSE 1
                                     END)
                               as totalpatientdays")
                )
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('p.id')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->totalpatientdays;
        }
        return json_encode($data);
    }
    
    public function patientDaysDay(){
 
        $results = [];
        for($i=1;$i<=30;$i++) { 
            // query data from database
            $beginTime = Carbon::now()->startOfDay()->subDays(30 - $i);
            $endTime = $beginTime->copy()->endOfDay();
            $results[] = $this->queryPatientDays($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->totalpatientdays;
            $record["label"]    = date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->totalpatientdays."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    public function patientDaysWeek(){
        
        $results = [];
        for($i=1;$i<=14;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfWeek()->subWeeks( 14 - $i);
            $endTime = $beginTime->copy()->endOfWeek();
            $results[] = $this->queryPatientDays($beginTime, $endTime);            
        }
                
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->totalpatientdays;
            $record["label"]    = date("m-d-Y", strtotime($value->begindate)) ." to ". date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->totalpatientdays."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    public function patientDaysMonth(){
               
        $results = [];
        for($i=1;$i<=12;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfMonth()->subMonths( 12 - $i);
            $endTime = $beginTime->copy()->endOfMonth();
            $results[] = $this->queryPatientDays($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->totalpatientdays;
            $record["label"]    = date("F-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->totalpatientdays."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }
    
    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_PD)->first();
            
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }
    
    protected function queryPatientDays( $beginTime, $endTime ){
        $now = Carbon::now();
        $beginDate = $beginTime->toDateString();
        $endDate = $endTime->toDateString();
        
        $totalPatientDays = 0;
        if( $beginTime <= $now ){
            if( $endTime > $now->endOfDay() ){
                $endTime = $now;
            }
            $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
                ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
                ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
                ->select(DB::raw("SUM (CASE 
                                        WHEN pv.dischargedate IS NOT NULL THEN
                                            CASE
                                        	   WHEN pv.admitdate > '$beginTime' AND pv.dischargedate >= '$endTime' THEN
                                        		    CASE 
                                                        WHEN date_trunc('day', pv.admitdate) = pv.admitdate THEN
                                                            DATE_PART('day', date_trunc('day', timestamp '$endTime') - date_trunc('day', pv.admitdate)) + 1
                                                        ELSE
                                                            DATE_PART('day', date_trunc('day', timestamp '$endTime') - date_trunc('day', pv.admitdate))
                                                    END
                                        	   WHEN pv.admitdate > '$beginTime' AND pv.dischargedate < '$endTime' THEN
                                                    CASE 
                                                        WHEN date_trunc('day', pv.admitdate) = pv.admitdate  THEN
                                                            DATE_PART('day', date_trunc('day', pv.dischargedate) - date_trunc('day', pv.admitdate)) + 1
                                                        ELSE
                                                            DATE_PART('day', date_trunc('day', pv.dischargedate) - date_trunc('day', pv.admitdate))
                                                    END
                                        	   WHEN pv.admitdate <= '$beginTime' AND pv.dischargedate >= '$endTime' THEN
                                                    DATE_PART('day', date_trunc('day', timestamp '$endTime') - date_trunc('day', timestamp '$beginTime')) + 1
                                        	   WHEN pv.admitdate <= '$beginTime' AND pv.dischargedate < '$endTime' THEN
                                        		    DATE_PART('day', date_trunc('day', pv.dischargedate) - date_trunc('day', timestamp '$beginTime')) + 1
                                               ELSE 0
                                        	END
                                         ELSE
                                        	CASE
                                               WHEN pv.admitdate > '$beginTime' THEN
                                                    CASE 
                                                        WHEN date_trunc('day', pv.admitdate) = pv.admitdate THEN
                                                            DATE_PART('day', date_trunc('day', timestamp '$endTime') - date_trunc('day', pv.admitdate)) + 1
                                                        ELSE
                                                            DATE_PART('day', date_trunc('day', timestamp '$endTime') - date_trunc('day', pv.admitdate))
                                                    END
                                        	   ELSE DATE_PART('day', date_trunc('day', timestamp '$endTime') - date_trunc('day', timestamp '$beginTime')) + 1
                                        	END	
                                         END)
                                       as totalpatientdays")
                    )
                ->whereNotNull('pv.admitdate')
                ->where('pv.admitdate', '<=', $endTime)
                ->where(function ($query) use ($beginTime){
                    $query->whereNull('pv.dischargedate')
                    ->orWhere('pv.dischargedate', '>', $beginTime);
                })
                ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
                ->where("pl.code","!=",Patient_location::$TYPE_ER)
                ->first();
            
            $totalPatientDays = (int)$results->totalpatientdays;
        }
        $data = (object)[
            'begindate' => $beginDate,
            'enddate' => $endDate,
            'totalpatientdays' => $totalPatientDays
        ];
        
        return $data;
    }

    public function patientDaysWeekFromtoEnd(Request $request){
	    $date = explode('to ', $request->input('FromToDate'));
	    $start = explode('-',$date[0]);
	    $end  = explode('-',$date[1]);

	    $from =  Carbon::parse($start[1].'-'.$start[0].'-'.$start[2]);
	    $to = Carbon::parse($end[1].'-'.$end[0].'-'.$end[2]);

	    $days = $from->diffInDays($to) + 1;
	    
	    $results = [];
	    for($i=1;$i<=$days;$i++) {
	        $beginTime = $from->copy()->startOfDay()->addDays($i-1);
	        $endTime = $beginTime->copy()->endOfDay();
	        $results[] = $this->queryPatientDays($beginTime, $endTime);
	    }

	    $data = [];
	    foreach ($results as $value) {
		    $record = [];
		    $record['y']    = $value->totalpatientdays;
		    $record["label"]    = date("m-d-Y", strtotime($value->enddate));
		    $record["indexLabel"] = "".$value->totalpatientdays."";
		    $data[] = $record;
	    }

	    return json_encode($data);
    }


    public function patientDaysMonthYear(Request $request){

	    $MonthYear =  new DateTime($request->input('MonthYear'));
	    $MonthYearDate  =  $MonthYear->format('y-m-d');
	    $from =  date('Y-m-d H:i:s', strtotime($MonthYearDate));
	    $to   = date('Y-m-t H:i:s', strtotime($MonthYearDate));
	    $numOfWeeks = Carbon::parse($to)->diffInWeeks(Carbon::parse($from)) + 1;
	    
	    $results = [];
	    $beginDate = Carbon::parse($from)->startOfWeek();
	    $endDate = $beginDate->copy()->endOfWeek();
	    for($i=1;$i<=$numOfWeeks;$i++) {
		    // query data from database
		    if($i > 1){
			    $beginDate->addWeek(1);
			    $endDate->addWeek(1);
		    }

		    $results[] = $this->queryPatientDays($beginDate, $endDate);
	    }

	    $data = [];
	    foreach ($results as $value) {
		    $record = [];
		    $record['y']    = $value->totalpatientdays;
		    $record["label"]    = date("m-d-Y", strtotime($value->begindate)) ." to ". date("m-d-Y", strtotime($value->enddate));
		    $record["indexLabel"] = "".$value->totalpatientdays."";
		    $data[] = $record;
	    }

	    return json_encode($data);
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_PD )
        ->first();
        
        return $record->metric_name;
    }
}
