<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Carbon\Carbon;

use App\Metric;
use App\Patient_visit;
use App\Patient_location;
use App\Patient_identification;
use App\Patient_type;
use App\Hospital_service;
use App\Provider;
use App\Traits\FavouriteMetricsTrait;


class MortalityratemetricController extends Controller
{
    use FavouriteMetricsTrait;

    public function count(Request $request){
        $this->registerFavouriteMetric($request);

        $data = [
            'count' => $this->mortalityRate2(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }

    public function mortalityrateNursingUnit(){
        $endTime = Carbon::now();
        $beginTime = $endTime->copy()->subDays(30);
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(DB::raw(
                "(select pvd.patientid, MAX(pvd.admitdate) as admitdate
                    from patient_visits pvd
                    group by pvd.patientid ) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate = pvdq.admitdate');
            })
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_identification::getTableName() . ' as pi', function($join){
                $join->on('pi.patientid','=','pv.patientid')->on('pi.patientdeathdateandtime','>=','pv.admitdate'); })
            ->select(DB::raw('pl.location, COUNT(DISTINCT(pi.id)) '))
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>=', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->where("pi.patientdeathindicator","=",true)
            ->whereBetween('pi.patientdeathdateandtime', [$beginTime, $endTime])
            ->groupBy('pl.id')
            ->get();
           
        $data = [];
        foreach ($record as $key => $value) {
            $data[$value->location] = $value->count;
        }
        return json_encode($data);
    }

    public function mortalityrateHospitalService(){
        $endTime = Carbon::now();
        $beginTime = $endTime->copy()->subDays(30);
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(DB::raw(
                "(select pvd.patientid, MAX(pvd.admitdate) as admitdate
                    from patient_visits pvd
                    group by pvd.patientid ) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate = pvdq.admitdate');
            })
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_identification::getTableName() . ' as pi', function($join){
                $join->on('pi.patientid','=','pv.patientid')->on('pi.patientdeathdateandtime','>=','pv.admitdate'); })
            ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
            ->select(DB::raw('hs.description, COUNT(DISTINCT(pi.id)) '))
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>=', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->where("pi.patientdeathindicator","=",true)
            ->whereBetween('pi.patientdeathdateandtime', [$beginTime, $endTime])
            ->groupBy('hs.id')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $data[$value->description] = $value->count;
        }
        
        return json_encode($data);
    }
    
    public function mortalityrateAdmittingProvider(){
        $endTime = Carbon::now();
        $beginTime = $endTime->copy()->subDays(30);
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(DB::raw(
                "(select pvd.patientid, MAX(pvd.admitdate) as admitdate
                    from patient_visits pvd
                    group by pvd.patientid ) as pvdq"
            ), function ($join) {
                $join->on('pv.patientid', '=', 'pvdq.patientid')
                ->whereRaw('pv.admitdate = pvdq.admitdate');
            })
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_identification::getTableName() . ' as pi', function($join){
                $join->on('pi.patientid','=','pv.patientid')->on('pi.patientdeathdateandtime','>=','pv.admitdate'); })
            ->join(Provider::getTableName().' as p','pv.admittingprovider','=','p.id')
            ->select(DB::raw('p.name_prefix, p.name_first, p.name_last, p.name_suffix, COUNT(DISTINCT(pi.id))'))
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>=', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->where("pi.patientdeathindicator","=",true)
            ->whereBetween('pi.patientdeathdateandtime', [$beginTime, $endTime])
            ->groupBy('p.id')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
        }
        
        return json_encode($data);
    }
    
    public function mortalityrateMonth(){
        $results = [];
        for($i=1;$i<=12;$i++) {
            // query data from database
            $beginTime = Carbon::now()->startOfMonth()->subMonths( 12 - $i);
            if( $i == 12 ){
                $endTime = Carbon::now()->endOfDay();
            } else {
                $endTime = $beginTime->copy()->endOfMonth();
            }
            
            $results[] = $this->queryMortalityRate($beginTime, $endTime);
        }
        
        $data = [];
        foreach ($results as $value) {
            $record = [];
            $record['y']    = $value->mortalityrate;
            $record["label"]    = date("F-Y", strtotime($value->enddate));
            $record["indexLabel"] = "".$value->mortalityrate."%";
            $data[] = $record;
        }
        return json_encode($data);
    }
    
    protected function queryMortalityRate( $beginTime, $endTime ){
        $admissions = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select('pv.id')
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>=', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->get()
            ->count();
        
        $deaths = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Patient_identification::getTableName() . ' as pi', function($join){
                $join->on('pi.patientid','=','pv.patientid')->on('pi.patientdeathdateandtime','>=','pv.admitdate'); })
            ->select('pi.id')
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>=', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->where("pi.patientdeathindicator","=",true)
            ->whereBetween('pi.patientdeathdateandtime', [$beginTime, $endTime])
            ->distinct()
            ->get()
            ->count();
        
        $cal = 0;
        if($admissions > 0){
            $cal = round(($deaths  / $admissions ) * 100);
            if($cal > 100){
                $cal = 100;
            }
        }
        
        $data = (object)[
           'begindate' => $beginTime->toDateString(),
           'enddate' => $endTime->toDateString(),
           'mortalityrate' => $cal
        ];
        
        return $data;
    }

    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_MR)->first();
        
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_MR )
        ->first();
        
        return $record->metric_name;
    }
}
