<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use App\Metric;
use App\Patient_visit;
use App\Patient_location;
use App\Patient_identification;
use App\Patient_type;
use App\Hospital_service;
use App\Provider;

class MortalityRateController extends Controller
{
    public function nursingUnit(){
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
        
        $title = $this->getTitle().' in Nursing Unit';
        $header = array("Unit", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function hospitalService(){
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
        
        $title = $this->getTitle().' in Hospital Service';
        $header = array( "Services", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function admittingProvider(){
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
        
        $title = $this->getTitle().' in Admitting Provider';
        $header = array( "Providers", "No of Patients");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_MR )
        ->first();
        
        return $record->metric_name;
    }
}
