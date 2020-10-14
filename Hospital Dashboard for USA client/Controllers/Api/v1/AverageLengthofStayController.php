<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Metric;
use App\Patient_location;
use App\Patient_type;
use App\Patient_visit;
use App\Hospital_service;

class AverageLengthofStayController extends Controller
{
    public function nursingUnit(){
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
        
        $title = $this->getTitle().' in Nursing Unit';
        $header = array("Unit", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function hospitalService(){
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
            
        $title = $this->getTitle().' in Hospital Service';
        $header = array( "Services", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_AS )
        ->first();
        
        return $record->metric_name;
    }
}
