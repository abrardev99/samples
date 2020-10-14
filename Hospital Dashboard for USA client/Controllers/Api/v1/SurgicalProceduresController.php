<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Metric;
use App\Patient_location;
use App\Patient_type;
use App\Patient_visit;
use App\Provider;
use App\Order;
use App\Hospital_service;
use Carbon\Carbon;

class SurgicalProceduresController extends Controller
{
    public function orderingProvider(){
        $record = Order::from(Order::getTableName().' as o')
            ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
            ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Provider::getTableName().' as p','o.provider_id','=','p.id')
            ->select(DB::raw('p.name_prefix,
                                p.name_first,
                                p.name_last,
                                p.name_suffix,
                                COUNT(o.id)'))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
            ->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
            ->groupBy('p.id')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->name_prefix.' '.$value->name_first.','.$value->name_last.' '.$value->name_suffix;
            $data[$key] = $value->count;
        }
        
        $title = $this->getTitle().' Ordering Provider';
        $header = array("Order Providers", "No of Orders");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function priority(){
        $record = Order::from(Order::getTableName().' as o')
            ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
            ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
            ->select(DB::raw('o.priority, COUNT(o.id)'))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
            ->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
            ->groupBy('o.priority')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->priority;
            $data[$key] = $value->count;
        }
        
        $title = $this->getTitle().' Priority';
        $header = array("Priority", "No of Orders");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function hospitalService(){
        $record = Order::from(Order::getTableName().' as o')
            ->join(Patient_visit::getTableName().' as pv', 'o.patientvisitid','=','pv.id')
            ->join(Patient_type::getTableName().' as pt','pv.patienttype','=','pt.id')
            ->join(Patient_location::getTableName().' as pl','pv.assignedpatientlocation','=','pl.id')
            ->join(Hospital_service::getTableName().' as hs','pv.hospitalservice','=','hs.id')
            ->select(DB::raw('hs.description, COUNT(o.id) '))
            ->where("pt.code" ,"=" ,Patient_type::$TYPE_O)
            ->where("pl.code" ,"!=" ,Patient_location::$TYPE_ER)
            ->where("o.order_type" ,"=" ,Order::$TYPE_SUR)
            ->where("o.requested_dttm" ,">=" ,Carbon::now()->subHours(24))
            ->groupBy('hs.id')
            ->get();
        
        $data = [];
        foreach ($record as $key => $value) {
            $key = $value->description;
            $data[$key] = $value->count;
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
        ->where('m.uid', '=', Metric::$TYPE_SP )
        ->first();
        
        return $record->metric_name;
    }
}
