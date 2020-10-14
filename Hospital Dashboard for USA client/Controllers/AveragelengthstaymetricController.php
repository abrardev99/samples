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
use App\Hospital_service;
use App\Traits\FavouriteMetricsTrait;



class AveragelengthstaymetricController extends Controller
{
    use FavouriteMetricsTrait;
    public function count(Request $request){

        $this->registerFavouriteMetric($request);

        $data = [
            'count' => $this->averageLengthofStay(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }

    public function averagelengthstayNursingUnit(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            //->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_type::getTableName() . ' as pt', function($join){
                $join->on('pt.id','=','pl.patienttypeid')->on('pt.id','=','pv.patienttype'); })
            ->select(DB::raw("pl.location, COUNT(pv.id) as inpatient, SUM (DATE_PART('day', ((NOW() +interval '23 hours' + interval '59 minutes' + interval '59 seconds' )- pv.admitdate ))) as lengthofstay"))
            ->whereNull("pv.dischargedate")
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->where("pl.code", "!=", Patient_location::$TYPE_ER)
            ->groupBy('pl.id')
            ->get();
            
        $data = [];
        foreach ($record as $value) {
            $cal = 0;
            $lengthOfStay = (int)$value->lengthofstay;
            $inpatient = $value->inpatient;
            if( $inpatient > 0 ){
                $cal = round( $lengthOfStay / $inpatient, 1 );
            }
            $data[$value->location] = $cal;
        }
        
        return json_encode($data);
    }

    public function averagelengthstayHospitalService(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            //->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_type::getTableName() . ' as pt', function($join){
                $join->on('pt.id','=','pl.patienttypeid')->on('pt.id','=','pv.patienttype'); })
            ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
            ->select(DB::raw("hs.description, COUNT(pv.id) as inpatient, SUM (DATE_PART('day', ((NOW() +interval '23 hours' + interval '59 minutes' + interval '59 seconds' )- pv.admitdate ))) as lengthofstay"))
            ->whereNull("pv.dischargedate")
            ->where("pt.code", "=", Patient_type::$TYPE_I)
            ->where("pl.code", "!=", Patient_location::$TYPE_ER)
            ->groupBy('hs.id')
            ->get();
            
            $data = [];
            foreach ($record as $value) {
                $cal = 0;
                $lengthOfStay = (int)$value->lengthofstay;
                $inpatient = $value->inpatient;
                if( $inpatient > 0 ){
                    $cal = round( $lengthOfStay / $inpatient, 1 );
                }
                $data[$value->description] = $cal;
            }

        return json_encode($data);
    }

    public function averagelengthstayDay(){
        $results = [];
        for($i=1;$i<=30;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfDay()->subDays(30 - $i);
            $endTime = $beginTime->copy()->endOfDay();
            
            $results[] = $this->queryAvgLenghtOfStay($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->avglengthofstay;
            $record["label"]    = date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->avglengthofstay."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    public function averagelengthstayWeek(){
        $results = [];
        for($i=1;$i<=14;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfWeek()->subWeeks( 14 - $i);
            $endTime = $beginTime->copy()->endOfWeek();
            
            $results[] = $this->queryAvgLenghtOfStay($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->avglengthofstay;
            $record["label"]    = date("m-d-Y", strtotime($value->begindate)). " to ". date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->avglengthofstay."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    public function averagelengthstayMonth(){
        $results = [];
        for($i=1;$i<=12;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfMonth()->subMonths( 12 - $i);
            if( $i == 12 ){
                $endTime = Carbon::now()->endOfDay();
            } else {
                $endTime = $beginTime->copy()->endOfMonth();
            }
            
            $results[] = $this->queryAvgLenghtOfStay($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->avglengthofstay;
            $record["label"]    = date("F-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->avglengthofstay."";
            $data[] = $record;
        }
        return json_encode($data);
    }

    public function averagelengthstayWeekFromtoEnd(Request $request) {
        $date = explode('to ', $request->input('FromToDate'));
        $start = explode('-',$date[0]);
        $end  = explode('-',$date[1]);
        
        $from =  Carbon::parse($start[1].'-'.$start[0].'-'.$start[2]);
        $to = Carbon::parse($end[1].'-'.$end[0].'-'.$end[2]);
        $days = $from->diffInDays($to) + 1;
        
        $results = [];
        for($i=1;$i<=$days;$i++) {
            // query data from database
            $beginTime = $from->copy()->startOfDay()->addDays($i-1);
            $endTime = $beginTime->copy()->endOfDay();
            $results[] = $this->queryAvgLenghtOfStay($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->avglengthofstay;
            $record["label"]    = date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->avglengthofstay."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    public function averagelengthstayMonthYear(Request $request) {        
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
            
            $results[] = $this->queryAvgLenghtOfStay($beginDate, $endDate);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->avglengthofstay;
            $record["label"]    = date("m-d-Y", strtotime($value->begindate)). " to ". date("m-d-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->avglengthofstay."";
            $data[] = $record;
        }
        
        return json_encode($data);
    }

    protected function queryAvgLenghtOfStay( $beginTime, $endTime ){
        $now = Carbon::now();
        $avgLengthOfStay = 0;
        $beginDate = $beginTime->toDateString();
        $endDate = $endTime->toDateString();
        
        if( $beginTime <= $now ){
            if( $endTime > $now->endOfDay() ){
                $endTime = $now;
            }
            
            $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
                //->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
                ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
                ->join(Patient_type::getTableName() . ' as pt', function($join){
                    $join->on('pt.id','=','pl.patienttypeid')->on('pt.id','=','pv.patienttype'); })
                ->select(DB::raw("  COUNT(pv.id) as inpatient, 
                                    SUM (CASE
                                            WHEN pv.dischargedate IS NOT NULL THEN
                                                DATE_PART('day', (pv.dischargedate + interval '23 hours' + interval '59 minutes' + interval '59 seconds') - pv.admitdate)
                                            ELSE
                                            	DATE_PART('day', (timestamp '$endTime' + interval '23 hours' + interval '59 minutes' + interval '59 seconds') - pv.admitdate)
                                            END                                  
                                        ) 
                                    as lengthofstay"))
                ->Where('pv.admitdate', '<=', $endTime)
                ->where(function ($query) use ($beginTime){
                    $query->whereNull('pv.dischargedate')
                    ->orWhere('pv.dischargedate', '>=', $beginTime);
                })
                ->where("pt.code", "=", Patient_type::$TYPE_I)
                ->where("pl.code", "!=", Patient_location::$TYPE_ER)
                ->get()
                ->first();
            
            $lengthOfStay = (int)$record->lengthofstay;
            $inpatient = $record->inpatient;
            if( $inpatient > 0 ){
                $avgLengthOfStay = round( $lengthOfStay / $inpatient, 1 );
            }    
        }
        
        $data = (object)[
            'begindate' => $beginDate,
            'enddate' => $endDate,
            'avglengthofstay' => $avgLengthOfStay
        ];
        
        return $data;
    }
    
    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_AS)->first();
        
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_AS )
        ->first();
        
        return $record->metric_name;
    }
}
