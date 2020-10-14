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
use Carbon\Carbon;

class VisitsController extends Controller
{
    public function provider(){
        $results = Patient_visit::from(Patient_visit::getTableName().' as pv' , Provider::getTableName().' as p')
            ->join(Patient_type::getTableName() .' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName() .' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p','pv.referringprovider','=','p.id')
            ->select(DB::raw('p.name_prefix, p.name_first, p.name_last, p.name_suffix, COUNT(pv.id)'))
            ->where("pv.admitdate" ,">=" ,Carbon::now()->subHours(24))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->groupBy('p.id')
            ->orderBy('p.id')
            ->get();
        
        $data = [];
        foreach ($results as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
        }
        
        $title = $this->getTitle().' in Providers';
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
        ->where('m.uid', '=', Metric::$TYPE_VI )
        ->first();
        
        return $record->metric_name;
    }
}
