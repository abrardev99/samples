<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Hospital_service;
use App\Metric;
use App\Patient_location;
use App\Patient_type;
use App\Patient_visit;
use App\Provider;
use Carbon\Carbon;

class InpatientDischargesController extends Controller
{
    public function nursingUnit(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
        ->select(DB::raw('pl.location, COUNT(pv.id) '))
        ->where("pv.dischargedate" ,">=" ,Carbon::now()->subHours(24))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->where("pl.code","!=",Patient_location::$TYPE_ER)
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
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
        ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
        ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
        ->join(Hospital_service::getTableName() .' as hs','pv.hospitalservice','=','hs.id')
        ->select(DB::raw('hs.description, COUNT(pv.id) '))
        ->where("pv.dischargedate" ,">=" ,Carbon::now()->subHours(24))
        ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
        ->where("pl.code","!=",Patient_location::$TYPE_ER)
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
    
    public function referringProvider(){
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv' , Provider::getTableName() .' as p')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p' ,'pv.referringprovider','=','p.id')
            ->select(DB::raw('p.name_prefix,
                        p.name_first,
                        p.name_last,
                        p.name_suffix,
                        COUNT(pv.id)'))
            ->where("pv.dischargedate" ,">=" ,Carbon::now()->subHours(24))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('p.id')
            ->get();
            
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
        }
        
        $title = $this->getTitle().' in Referring Provider';
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
        ->where('m.uid', '=', Metric::$TYPE_ID )
        ->first();
        
        return $record->metric_name;
    }
}
