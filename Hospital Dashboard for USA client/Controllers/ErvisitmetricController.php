<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
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


class ErvisitmetricController extends Controller
{
    use FavouriteMetricsTrait;
    use ZipCodeToCoordinates;

    public function count(Request $request){
        $this->registerFavouriteMetric($request);

        
        $data = [ 
            'count' => $this->erVisits(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }


    public function ervisitsProvider(){
        $results = Patient_visit::from(Patient_visit::getTableName().' as pv' , Provider::getTableName().' as p')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
            ->select(DB::raw('p.name_prefix, p.name_first, p.name_last, p.name_suffix, COUNT(pv.id)'))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
            ->groupBy('p.id')
            ->orderBy('p.id')
            ->get();

        $data = [];
        foreach ($results as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
        }
        return json_encode($data);
    }


    public function ervisitsHours(){
        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date_trunc('hour', pv.admitdate),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date_trunc('hour', pv.admitdate)"))
            ->orderBy(DB::raw(" date_trunc('hour', pv.admitdate)"))
            ->get();

        $hours =[];
        $past_hour = '';
        for($i=1;$i<=24;$i++) {
            if($past_hour == ''){
                $past_hour = date('Y-m-d H:i:s', strtotime('-1 days'));
            }
            $datetime = new DateTime($past_hour);
            $datetime->modify('+1 hour');
            $past_hour = $datetime->format('Y-m-d H:00:00');
            $hours[] = $datetime->format('Y-m-d H:00:00');
        }
      
        $data = [];
        foreach ($hours as $key => $value) {
            $flag = false;
            foreach ($results as $key => $result) {
                if($value == $result->date_trunc){
                    $record = [];
                    $record['y']    = (int)$result->count;
                    $record["label"]    = date("m-d-Y H:i", strtotime($value));
                    $record["indexLabel"] = "". $result->count."";
                    $data[] = $record;
                    $flag = true;
                    break;
                }
            }
            if( !$flag ) {
                $record = [];
                $record['y'] = 0;
                $record["label"] = date("m-d-Y H:i", strtotime($value));
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
        }
        return json_encode($data);
    }

    public function ervisitsDay(){
        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('day', pv.admitdate)),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subDays(30)) 
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date_trunc('day', pv.admitdate)"))
            ->orderBy(DB::raw(" date_trunc('day', pv.admitdate)"))
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
            $flag = false;
            foreach ($results as $key => $result) {
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

    public function ervisitsWeek(){
        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('week', pv.admitdate)),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subDays(90))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
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

    public function ervisitsMonth(){

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('month', pv.admitdate)),   COUNT(pv.id)"))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subDays(365))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date_trunc('month', pv.admitdate)"))
            ->orderBy(DB::raw(" date_trunc('month', pv.admitdate)"))
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
                if($value == date("F-Y", strtotime($result->date))){
                    $record = [];
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


    public function ervisitsweekfromtoend(Request $request){
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
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('day', pv.admitdate)),   COUNT(pv.id)"))
            ->whereBetween('pv.admitdate', [$from , $to])
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
            ->groupBy(DB::raw(" date_trunc('day', pv.admitdate)"))
            ->orderBy(DB::raw(" date_trunc('day', pv.admitdate)"))
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
            $flag = false;
            foreach ($results as $key => $result) {
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


    public function ervisitsmonthyear(Request $request){

        $MonthYear =  new DateTime($request->input('MonthYear'));
        $MonthYearDate  =  $MonthYear->format('y-m-d');
        $from =  date('Y-m-d H:i:s', strtotime($MonthYearDate));
        $to   = date('Y-m-t H:i:s', strtotime($MonthYearDate));

        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw(" date(date_trunc('week', pv.admitdate)),   COUNT(pv.id)"))
            ->whereBetween('pv.admitdate', [$from , $to])
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
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
        $record = Patient_visit::from(Patient_visit::getTableName().' as pv')
            ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient::getTableName() . ' as p','p.id','=','pv.patientid')
            ->select('p.address_zipcode')
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->where("pl.code" ,"=" ,Patient_location::$TYPE_ER)
            ->get('address_zipcode')->toArray();

        $record = array_filter($record, function ($zipCode) {
            return $zipCode['address_zipcode']!== null;
        });

        $coordinates = array_map(function($zipCode) {
            return self::getCoordinatesByZipCode($zipCode['address_zipcode']);
        }, $record);

        $coordinates= array_values($coordinates);
        return json_encode($coordinates);
    }

    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_ER)->first();

        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],

        ];

        return json_encode($data);
    }

    public function ervisitstimespent(){
        $results = Patient_visit::from(Patient_visit::getTableName().' as pv', Provider::getTableName().' as p')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient::getTableName() .' as p','p.id','=','pv.patientid')
            ->select(DB::raw('p.name_first, p.name_last,pv.admitdate,pv.dischargedate'))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(1))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_E)
            ->get();


        $data=[];
        $total_hours=0;

        foreach($results as $result){

            $date1 = strtotime($result->admitdate);
            $date2 = strtotime($result->dischargedate);

            $seconds =$date2-$date1;
            $total_hours+=$seconds;
        }
        
        if( sizeof($results) > 0 ){
            $total_hours = (int)$total_hours/sizeof($results);
        }
        $data=["<b>Average Time in the ER - Last Hour</b>"=>gmdate("H:i",$total_hours)];

        return json_encode($data);

    }

    
    protected  function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_ER )
        ->first();
        
        return $record->metric_name;
    }

}
