<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use App\Metric;
use App\Patient_visit;
use App\Patient_type;
use App\Patient_location;
use App\Provider;

class ReadmissionRateController extends Controller
{
    public function nursingUnit(){
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
        
        $title = $this->getTitle().' in Nursing Unit';
        $header = array("Unit", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function admittingProvider(){
        
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
        
        $title = $this->getTitle().' in Admitting Provider';
        $header = array("Providers", "No of Patients");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_RR )
        ->first();
        
        return $record->metric_name;
    }
}
