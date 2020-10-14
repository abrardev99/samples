<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Metric;

class PatientRegistrationController extends Controller
{
    public function nursingUnit(){
        $data  = [
            '1 South Tower '   => '24',
            '1 North Tower'    => '30',
            '2 South Tower '   => '36',
            '2 North Tower '   => '26',
            '3 East Tower '    => '6',
            'CVICU '           => '2',
            'ICU'              => '9',
        ];
        
        $title = $this->getTitle().' in Nursing Unit';
        $header = array("Unit", "Average");
        
        return response()->json( [
            'title' => $title,
            'header' => $header,
            'data' => $data
        ] );
    }
    
    public function hospitalService(){
        $data  = [
            'Medical Service '   => '19',
            'Surgical Service'   => '24',
            'Urology Service '   => '30',
            'Pulmonary Service ' => '38',
            'Cardiac Service '   => '39',
        ];
        
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
        ->where('m.uid', '=', Metric::$TYPE_PR )
        ->first();
        
        return $record->metric_name;
    }
}
