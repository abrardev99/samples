<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Metric;
use App\Patient_visit;
use App\Patient_type;
use App\Patient_location;
use App\Provider;
use App\Hospital_service;
use Carbon\Carbon;

class PatientDaysController extends Controller
{
    public function nursingUnit(){
        $beginTime = Carbon::now()->startOfDay();
        $endTime = Carbon::now();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select('pl.location',
                DB::raw("SUM (CASE
                                            WHEN pv.admitdate > '$beginTime' THEN 0
                                            ELSE 1
                                         END)
                                   as totalpatientdays")
                )
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('pl.id')
            ->get();
            
        $data = [];
        foreach ($record as $value) {
            $data[$value->location] = $value->totalpatientdays;
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
        $beginTime = Carbon::now()->startOfDay();
        $endTime = Carbon::now();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
            ->select('hs.description',
                DB::raw("SUM (CASE
                                        WHEN pv.admitdate > '$beginTime' THEN 0
                                        ELSE 1
                                     END)
                                   as totalpatientdays")
                )
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('hs.id')
            ->get();
            
        $data = [];
        foreach ($record as $value) {
            $data[$value->description] = $value->totalpatientdays;
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
        
        $beginTime = Carbon::now()->startOfDay();
        $endTime = Carbon::now();
        
        $record = Patient_visit::from(Patient_visit::getTableName() .' as pv')
            ->join(Patient_type::getTableName() . ' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() . ' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
            ->select('p.name_first', 'p.name_last', 'p.name_suffix',
                DB::raw("SUM (CASE
                                            WHEN pv.admitdate > '$beginTime' THEN 0
                                            ELSE 1
                                         END)
                                   as totalpatientdays")
                )
            ->whereNotNull('pv.admitdate')
            ->where('pv.admitdate', '<=', $endTime)
            ->where(function ($query) use ($beginTime){
                $query->whereNull('pv.dischargedate')
                ->orWhere('pv.dischargedate', '>', $beginTime);
            })
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_I)
            ->where("pl.code","!=",Patient_location::$TYPE_ER)
            ->groupBy('p.id')
            ->get();
            
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->totalpatientdays;
        }
        
        $title = $this->getTitle().' in Referring Provider';
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
        ->where('m.uid', '=', Metric::$TYPE_PD )
        ->first();
        
        return $record->metric_name;
    }
}
