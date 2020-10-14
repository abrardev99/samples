<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Patient_visit;
use App\Patient_type;
use App\Patient_location;
use App\Provider;
use App\Metric;

class AverageDailyCensusController extends Controller
{
    public function nursingUnit(){
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
        ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
        ->select(DB::raw('pl.location, COUNT(pv.id) '))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->whereNull("pv.dischargedate")
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
    
    public function admittingProvider(){
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv' , Provider::getTableName() .' as p')
        ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
        ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
        ->select(DB::raw('p.name_prefix, p.name_first,p.name_last,p.name_suffix,COUNT(pv.id)'))
        ->whereNull("pv.dischargedate")
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->groupBy('p.id')
        ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
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
        ->where('m.uid', '=', Metric::$TYPE_AC )
        ->first();
        
        return $record->metric_name;
    }
}
