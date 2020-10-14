<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

use App\Metric;
use App\Patient_location;
use App\Patient_type;
use App\Patient_visit;

class BedUtilizationController extends Controller
{
    public function nursingUnit(){
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
        
        $title = $this->getTitle().' in Hospital Service';
        $header = array( "Services", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function emptyBeds(){
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
        
        $title = $this->getTitle().' in Empty Beds';
        $header = array( "Unit", "Empty Beds");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_BU )
        ->first();
        
        return $record->metric_name;
    }
}
