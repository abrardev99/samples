<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use App\Metric;
use App\Patient_visit;
use App\Patient_type;
use App\Patient_location;
use App\Provider;
use Session;

use App\Traits\FavouriteMetricsTrait;


class ReadmissionratemetricController extends Controller
{
    use FavouriteMetricsTrait;

    public function count(Request $request){

        $this->registerFavouriteMetric($request);
        
        $data = [ 
            'count' => $this->readmissionRate2(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }

    public function readmissionrateNursingUnit(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(DB::raw(
            "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate');
            })
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
        ->select(DB::raw('pl.location, COUNT(pv.id) '))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
        ->groupBy('pl.id')
        ->get();
        
        $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select('pv.patientid')
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
        ->distinct()
        ->count();
        
        $data = [];
        foreach ($record as $value) {
            $cal = 0;
            if($totalInpatients > 0){
                $cal = ($value->count / $totalInpatients) * 100;
                if( $cal > 100 ){
                    $cal = 100;
                }
            }
            
            $data[$value->location] = round($cal) . '%';
        }
        return json_encode($data);
    }

    public function readmissionrateAdmittingProvider(){
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(DB::raw(
            "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate'); 
            })
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
        ->select(DB::raw('p.name_prefix,
                        p.name_first,
                        p.name_last,
                        p.name_suffix,
                        COUNT(pv.id)'))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
        ->groupBy('p.id')
        ->get();
        
        $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select('pv.patientid')
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
        ->distinct()
        ->count();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $cal = 0;
            if($totalInpatients > 0){
                $cal = ($value->count / $totalInpatients) * 100;
                if( $cal > 100 ){
                    $cal = 100;
                }
            }
            $data[$key] = round($cal) . '%';
        }
                        
        return json_encode($data);
    }
    
    public function readmissionrateMonth(){
        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(DB::raw(
            "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate');
            })
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select(DB::raw("date(date_trunc('month', pv.admitdate)), COUNT(pv.id) "))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
        ->groupBy(DB::raw(" date(date_trunc('month', pv.admitdate))"))
        ->orderBy(DB::raw(" date(date_trunc('month', pv.admitdate))"))
        ->get();
        
        $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select('pv.patientid')
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
        ->distinct()
        ->count();
        
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
                if($value == date("F-Y", strtotime($result->date))){
                    $record = [];
                    $cal = 0;
                    if($totalInpatients > 0){
                        $cal = round(($result->count / $totalInpatients) * 100);
                        if( $cal > 100 ){
                            $cal = 100;
                        }
                    }
                    $record['y'] = $cal;
                    $record["label"] = date("F-Y", strtotime($result->date));
                    $record["indexLabel"] =  "". $cal ."%";
                    $data[] = $record;
                    $flag = true;
                    break;
                }
            }
            if(!$flag){
                $record = [];
                $record['y'] = 0;
                $record["label"] = $value;
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
        }
        
        return json_encode($data);
    }
    
    public function readmissionrateQuarter(){
        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(DB::raw(
            "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate');
            })
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select(DB::raw("date(date_trunc('quarter', pv.admitdate)), COUNT(pv.id) "))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '3 years')")
        ->groupBy(DB::raw(" date(date_trunc('quarter', pv.admitdate))"))
        ->orderBy(DB::raw(" date(date_trunc('quarter', pv.admitdate))"))
        ->get();
        
        $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select('pv.patientid')
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '3 year')")
        ->distinct()
        ->count();
        
        $quarters = [];
        $past_date = '';
        for($i=1;$i<=12;$i++) {
            if($past_date == ''){
                $past_date = date('Y-m-d', strtotime('-3 years'));
            }
            $datetime = new DateTime($past_date);
            $datetime->modify('+3 months');
            $past_date = $datetime->format('F-Y');
            $quarters[] = $datetime;
        }
        
        $data = [];
        foreach ($quarters as $value) {
            $flag = false;
            foreach ($results as $result) {
                $datetime = new DateTime(date('Y-m-d', strtotime($result->date)));
                if($this->isAqualQuarter($value, $datetime)){
                    $record = [];
                    $cal = 0;
                    if($totalInpatients > 0){
                        $cal = round(($result->count / $totalInpatients) * 100);
                        if( $cal > 100 ){
                            $cal = 100;
                        }
                    }
                    $record['y'] = $cal;
                    $record["label"] = $datetime->format('Q'. $this->getQuarter($value) .'-Y');
                    $record["indexLabel"] =  "". $cal ."%";
                    $data[] = $record;
                    $flag = true;
                    break;
                }
            }
            if(!$flag){
                $record = [];
                $record['y'] = 0;
                $record["label"] = $value->format('Q'. $this->getQuarter($value) .'-Y');
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
        }
        
        return json_encode($data);
    }
    
    public function readmissionrateYear(){
        $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(DB::raw(
            "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate');
            })
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select(DB::raw("date(date_trunc('year', pv.admitdate)), COUNT(pv.id) "))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '5 years')")
        ->groupBy(DB::raw(" date(date_trunc('year', pv.admitdate))"))
        ->orderBy(DB::raw(" date(date_trunc('year', pv.admitdate))"))
        ->get();
        
        $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->select('pv.patientid')
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereRaw("pv.admitdate > (NOW() - INTERVAL '5 year')")
        ->distinct()
        ->count();
        
        $years = [];
        $past_date = '';
        for($i=1;$i<=5;$i++) {
            if($past_date == ''){
                $past_date = date('Y-m-d', strtotime('-5 years'));
            }
            $datetime = new DateTime($past_date);
            $datetime->modify('+1 year');
            $past_date = $datetime->format('F-Y');
            $years[] = $datetime->format('Y');
        }
        
        $data = [];
        foreach ($years as $value) {
            $flag = false;
            foreach ($results as $result) {
                if($value == date("Y", strtotime($result->date))){
                    $record = [];
                    $cal = 0;
                    if($totalInpatients > 0){
                        $cal = round(($result->count / $totalInpatients) * 100);
                        if( $cal > 100 ){
                            $cal = 100;
                        }
                    }
                    $record['y'] = $cal;
                    $record["label"] = date("Y", strtotime($result->date));
                    $record["indexLabel"] =  "". $cal ."%";
                    $data[] = $record;
                    $flag = true;
                    break;
                }
            }
            if(!$flag){
                $record = [];
                $record['y'] = 0;
                $record["label"] = $value;
                $record["indexLabel"] = "0";
                $data[] = $record;
            }
        }
        return json_encode($data);
    }
    
    protected function getQuarter(DateTime $date){
        $quarter = 1;
        $month = $date->format('n') ;
        
        if ($month < 4) {
            $quarter = 1;
        } elseif ($month > 3 && $month < 7) {
            $quarter = 2;
        } elseif ($month > 6 && $month < 10) {
            $quarter = 3;
        } elseif ($month > 9) {
            $quarter = 4;
        }
        
        return $quarter;
    }
    
    protected  function isAqualQuarter($date1, $date2){
        $year1 = $date1->format('y');
        $year2 = $date2->format('y'); 
        
        if(($year1 == $year2) && ($this->getQuarter($date1) == $this->getQuarter($date2))){
            return true;
        }
        
        return false;
    }
    
    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_RR)->first();
        
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }

    public function readmissionrateQuarterFromtoEnd(Request $request){
    	$quarter = $request->input('FromToDate');
    	$year = substr($quarter,3);

	    $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
		    ->join(DB::raw(
			    "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
		    ), function ($join) {
			    $join->on('pv.patientid', '=', 'pvdq.patientid')
				    ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate');
		    })
		    ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
		    ->select(DB::raw("date(date_trunc('month', pv.admitdate)), COUNT(pv.id) "))
		    ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
		    ->whereRaw("date_part('year', pv.admitdate)=".$year)
		    ->groupBy(DB::raw(" date(date_trunc('month', pv.admitdate))"))
		    ->orderBy(DB::raw(" date(date_trunc('month', pv.admitdate))"))
		    ->get();

	    $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
		    ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
		    ->select('pv.patientid')
		    ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
		    ->whereRaw("pv.admitdate > (NOW() - INTERVAL '1 year')")
		    ->distinct()
		    ->count();

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
			    if($value == date("F-Y", strtotime($result->date))){
				    $record = [];
				    $cal = 0;
				    if($totalInpatients > 0){
					    $cal = round(($result->count / $totalInpatients) * 100);
					    if( $cal > 100 ){
					        $cal = 100;
					    }
				    }
				    $record['y'] = $cal;
				    $record["label"] = date("F-Y", strtotime($result->date));
				    $record["indexLabel"] =  "". $cal ."%";
				    $data[] = $record;
				    $flag = true;
				    break;
			    }
		    }
		    if(!$flag){
			    $record = [];
			    $record['y'] = 0;
			    $record["label"] = $value;
			    $record["indexLabel"] = "0";
			    $data[] = $record;
		    }
	    }

	    return json_encode($data);
    }

    public function readmissionrateMonthYear(Request $request){
    	$year = $request->input('MonthYear');

	    $results = Patient_visit::from(Patient_visit::getTableName() .' as pv')
		    ->join(DB::raw(
			    "(select pvd.patientid, pvd.dischargedate, (pvd.dischargedate + INTERVAL '30 day') as readmitdate
                from patient_visits pvd
                where pvd.dischargedate is not null
                and pvd.patienttype = 1) as pvdq"
		    ), function ($join) {
			    $join->on('pv.patientid', '=', 'pvdq.patientid')
				    ->whereRaw('pv.admitdate between pvdq.dischargedate and pvdq.readmitdate');
		    })
		    ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
		    ->select(DB::raw("date(date_trunc('quarter', pv.admitdate)), COUNT(pv.id) "))
		    ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
		    ->whereRaw("date_part('year', pv.admitdate)=".$year)
		    ->groupBy(DB::raw(" date(date_trunc('quarter', pv.admitdate))"))
		    ->orderBy(DB::raw(" date(date_trunc('quarter', pv.admitdate))"))
		    ->get();

	    $totalInpatients = Patient_visit::from(Patient_visit::getTableName() .' as pv')
		    ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
		    ->select('pv.patientid')
		    ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
		    ->whereRaw("pv.admitdate > (NOW() - INTERVAL '3 year')")
		    ->distinct()
		    ->count();

	    $quarters = [];
	    $past_date = '';
	    for($i=1;$i<=12;$i++) {
		    if($past_date == ''){
			    $past_date = date('Y-m-d', strtotime('-3 years'));
		    }
		    $datetime = new DateTime($past_date);
		    $datetime->modify('+3 months');
		    $past_date = $datetime->format('F-Y');
		    $quarters[] = $datetime;
	    }

	    $data = [];
	    foreach ($quarters as $value) {
		    $flag = false;
		    foreach ($results as $result) {
			    $datetime = new DateTime(date('Y-m-d', strtotime($result->date)));
			    if($this->isAqualQuarter($value, $datetime)){
				    $record = [];
				    $cal = 0;
				    if($totalInpatients > 0){
					    $cal = round(($result->count / $totalInpatients) * 100);
					    if( $cal > 100 ){
					        $cal = 100;
					    }
				    }
				    $record['y'] = $cal;
				    $record["label"] = $datetime->format('Q'. $this->getQuarter($value) .'-Y');
				    $record["indexLabel"] =  "". $cal ."%";
				    $data[] = $record;
				    $flag = true;
				    break;
			    }
		    }
		    if(!$flag){
			    $record = [];
			    $record['y'] = 0;
			    $record["label"] = $value->format('Q'. $this->getQuarter($value) .'-Y');
			    $record["indexLabel"] = "0";
			    $data[] = $record;
		    }
	    }


    	return json_encode($data);

    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_RR )
        ->first();
        
        return $record->metric_name;
    }
}
