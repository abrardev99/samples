<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use DB;
use DateTime;
use App\Metric;
use App\Patient;
use App\Patient_visit;
use App\Patient_type;
use App\Patient_location;
use App\Hospital_service;
use App\Provider;
use Carbon\Carbon;
use Session;

use App\Traits\FavouriteMetricsTrait;
use App\Traits\ZipCodeToCoordinates;

class AdmissionmetricController extends Controller
{
    use FavouriteMetricsTrait;
    use ZipCodeToCoordinates;
    
    public function count(Request $request){
        $this->registerFavouriteMetric($request);

        $data = [
            'count' => $this->admissions(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }
    
    public function admissionNursingUnit(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName(). ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw('pl.location, COUNT(pv.id) '))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('pl.id')
            ->get();

        $data = [];
        foreach ($record as $key => $value) {
            $data[$value->location] = $value->count;
        }
        return json_encode($data);
    }

    public function admissionHospitalService(){

        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
            ->select(DB::raw('hs.description, COUNT(pv.id) '))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('hs.id')
            ->get();

        $data = [];
        foreach ($record as $key => $value) {
            $data[$value->description] = $value->count;
        }

        return json_encode($data);
    }


    public function admissionReferringProvider(){

        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv' , Provider::getTableName() .' as p')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
            ->select(DB::raw('p.name_prefix, p.name_first, p.name_last, p.name_suffix, COUNT(pv.id)'))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('p.id')
            ->get();

        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
        }

        return json_encode($data);
    }

    public function admissionDay(){

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('day', pv.admitdate)),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subDays(30)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('pv.admitdate')
            ->orderBy('pv.admitdate')
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
        foreach ($dates as $key => $value) {
            foreach ($results as $key => $result) {
                if($value == $result->date){
                    $record = [];
                    $record['y']    = (int)$result->count;
                    $record["label"]    = date("m-d-y", strtotime($result->date));
                    $record["indexLabel"] = "". $result->count."";
                    $data[] = $record;
                     break;
                }
            } 
            if( !in_array(date("m-d-y", strtotime($value)), array_column($data, 'label'))) { 
                $record = [];
                $record['y']    = 0;
                $record["label"]    = date("m-d-y", strtotime($value));
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
                 
        }
        return json_encode($data);
    }

    public function admissionWeek(){

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('week', pv.admitdate)),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subDays(90)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date(date_trunc('week', pv.admitdate))"))
            ->orderBy(DB::raw(" date(date_trunc('week', pv.admitdate))"))
            ->get();


        $weekdates = [];
	    for($i=1; $i<=14; $i++){
		    $start = Carbon::now()->startOfWeek()->subWeeks( 14 - $i);
		    $end = $start->copy()->endOfWeek();
		    $weekdates[] = $start->format('m-d-y') . ' to ' .  $end->format('m-d-y');
	    }

        $model_data = [];
        foreach ($results as $key => $value) {
            $record = [];
            $record['y']    = (int)$value->count;
            $record["label"]    = $this->getStartAndEndDate($value->date);
            $record["indexLabel"] = "".$value->count."";
            $model_data[] = $record;
        }

        $actual_data = [];
        foreach ($weekdates as $key => $date) {
            foreach ($model_data as $key => $data) {
                if($date == $data["label"]){
                    $record=[];
                    $record['y']=$data['y'];
                    $record["label"]= $data["label"];
                    $record["indexLabel"]=$data["indexLabel"];
                    $actual_data[]=$record;
                    break;
                }
            }
            if( !in_array($date, array_column($actual_data, 'label'))) { 
                $record=[];
                $record['y']= 0;
                $record["label"]= $date;
                $record["indexLabel"]="0";
                $actual_data[]=$record;
            }
        }
        return json_encode($actual_data);
        
    }

    public function admissionMonth(){

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('month', pv.admitdate)),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subDays(365)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date(date_trunc('month', pv.admitdate))"))
            ->orderBy(DB::raw(" date(date_trunc('month', pv.admitdate))"))
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
        foreach ($months as $key => $value) {
            foreach ($results as $key => $result) {
                $record = [];
                if($value === date("F-Y", strtotime($result->date))){
                    
                    $record['y']    = (int)$result->count;
                    $record["label"]    = date("F-Y", strtotime($result->date));
                    $record["indexLabel"] =  "".$result->count."";
                    $data[] = $record;
                    break;
                }
            }
            if( !in_array($value, array_column($data, 'label'))) { 
                $record = [];
                $record['y']    = 0;
                $record["label"]    = $value;
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
        }
        return json_encode($data);
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


    public function admissionweekfromtoend(Request $request){
        $date = explode('to ', $request->input('FromToDate'));
        $start = explode('-',$date[0]);
        $end  = explode('-',$date[1]);

        $startdateTime = new DateTime($start[1].'-'.$start[0].'-'.$start[2]);
        $start_date = $startdateTime->format('d-m-y');
        $enddateTime =  new DateTime($end[1].'-'.$end[0].'-'.$end[2]);
        $end_date  =  $enddateTime->format('d-m-y')." 23:59:59" ;

        $from =  date('Y-m-d H:i:s', strtotime($start_date));
        $to = date('Y-m-d H:i:s', strtotime($end_date));

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('day', pv.admitdate)),   COUNT(pv.id)"))
            ->whereBetween('pv.admitdate', [$from , $to]) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('pv.admitdate')
            ->orderBy('pv.admitdate')
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
        foreach ($dates as $key => $value) {
            foreach ($results as $key => $result) {
                if($value == $result->date){
                    $record = [];
                    $record['y']    = (int)$result->count;
                    $record["label"]    = date("m-d-y", strtotime($result->date));
                    $record["indexLabel"] = "". $result->count."";
                    $data[] = $record;
                    break;
                }
            }

            if( !in_array(date("m-d-y", strtotime($value)), array_column($data, 'label'))) { 
                $record = [];
                $record['y']    = 0;
                $record["label"]    = date("m-d-y", strtotime($value));
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
        }
        return json_encode($data);
    }


    public function admissionmonthyear(Request $request){

        $MonthYear =  new DateTime($request->input('MonthYear'));
        $MonthYearDate  =  $MonthYear->format('y-m-d');
        $from =  date('Y-m-d H:i:s', strtotime($MonthYearDate));
        $to   = date('Y-m-t H:i:s', strtotime($MonthYearDate));

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('week', pv.admitdate)),   COUNT(pv.id)"))
            ->whereBetween('pv.admitdate', [$from , $to]) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date(date_trunc('week', pv.admitdate))"))
            ->orderBy(DB::raw(" date(date_trunc('week', pv.admitdate))"))
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
        foreach ($results as $key => $value) {
            $record = [];
            $record['y']    = (int)$value->count;
            $record["label"]    = $this->getStartAndEndDate($value->date);
            $record["indexLabel"] = "$value->count";
            $model_data[] = $record;
        }
        $actual_data = [];
        foreach ($weekdates as $key => $date) {
            foreach ($model_data as $key => $data) {
                if($date == $data["label"]){
                    $record=[];
                    $record['y']=$data['y'];
                    $record["label"]=$data["label"];
                    $record["indexLabel"]=$data["indexLabel"];
                    $actual_data[]=$record;
                    break;
                }
            }

            if( !in_array($date, array_column($actual_data, 'label'))) { 
                $record=[];
                $record['y']= 0;
                $record["label"]=$date;
                $record["indexLabel"]="0";
                $actual_data[]=$record;
            }
        }
        return json_encode($actual_data);

    }
    
    public function getMapData(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
        ->join(Patient::getTableName() . ' as p','p.id','=','pv.patientid')
        ->select('p.address_zipcode')
        ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->where("pl.code","!=",Patient_location::$TYPE_ER)
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
    
    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_AD)->first();
        
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_AD )
        ->first();
        
        return $record->metric_name;
    }
}
