<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Metric;
use App\Traits\FavouriteMetricsTrait;



class PatientregistrationmetricController extends Controller
{
    use FavouriteMetricsTrait;
   	public function count(Request $request){
        $this->registerFavouriteMetric($request);
        
        $data = [
            'count' => $this->patientRegistration(),
            'title' => $this->getTitle()
        ];
        
        return json_encode($data);
    }

    public function patientregistrationNursingUnit(){
        $data  = [
            '1 South Tower '   => '24',
            '1 North Tower'    => '30',
            '2 South Tower '   => '36',
            '2 North Tower '   => '26',
            '3 East Tower '    => '6',
            'CVICU '           => '2',
            'ICU'              => '9',
        ];
        return json_encode($data);
    }

    public function patientregistrationHospitalService(){
        $data  = [
            'Medical Service '   => '19',
            'Surgical Service'   => '24',
            'Urology Service '   => '30',
            'Pulmonary Service ' => '38',
            'Cardiac Service '   => '39',
        ];
        return json_encode($data);
    }

    public function patientregistrationDay(){
        $data  = [
            [ "y" => 95, "label" => "01/07/2019", "indexLabel" => "95" ],
            [ "y" => 115, "label" => "02/07/2019", "indexLabel" => "115" ],
            [ "y" => 22, "label" => "03/07/2019", "indexLabel" => "22" ],
            [ "y" => 81, "label" => "04/07/2019", "indexLabel" => "81" ],
            [ "y" => 51, "label" => "05/07/2019", "indexLabel" => "51" ],
            [ "y" => 31, "label" => "06/07/2019", "indexLabel" => "31" ],
            [ "y" => 71, "label" => "07/07/2019", "indexLabel" => "71" ],
            [ "y" => 91, "label" => "08/07/2019", "indexLabel" => "91" ],
            [ "y" => 112, "label" => "09/07/2019", "indexLabel" => "112" ],
            [ "y" => 61, "label" => "10/07/2019", "indexLabel" => "61" ],
            [ "y" => 81, "label" => "11/07/2019", "indexLabel" => "81" ],
            [ "y" => 51, "label" => "12/07/2019", "indexLabel" => "51" ],
            [ "y" => 15, "label" => "13/07/2019", "indexLabel" => "15" ],
            [ "y" => 22, "label" => "14/07/2019", "indexLabel" => "22" ],
            [ "y" => 81, "label" => "15/07/2019", "indexLabel" => "81" ],
            [ "y" => 62, "label" => "16/07/2019", "indexLabel" => "62" ],
            [ "y" => 76, "label" => "17/07/2019", "indexLabel" => "76" ],
            [ "y" => 100, "label" => "18/07/2019", "indexLabel" => "100" ],
            [ "y" => 60, "label" => "19/07/2019", "indexLabel" => "60" ],
            [ "y" => 80, "label" => "20/07/2019", "indexLabel" => "80" ],
            [ "y" => 35, "label" => "21/07/2019", "indexLabel" => "35" ],
            [ "y" => 150, "label" => "22/07/2019", "indexLabel" => "150" ],
            [ "y" => 16, "label" => "23/07/2019", "indexLabel" => "16" ],
            [ "y" => 24, "label" => "24/07/2019", "indexLabel" => "24" ],
            [ "y" => 9, "label" => "25/07/2019", "indexLabel" => "9" ],
            [ "y" => 44, "label" => "26/07/2019", "indexLabel" => "44" ],
            [ "y" => 16, "label" => "27/07/2019", "indexLabel" => "16" ],
            [ "y" => 21, "label" => "28/07/2019", "indexLabel" => "21" ],
            [ "y" => 46, "label" => "29/07/2019", "indexLabel" => "46" ],
            [ "y" => 89, "label" => "30/07/2019", "indexLabel" => "89" ]
        ];
        return json_encode($data);
    }

    public function patientregistrationWeek(){
        $data  = [
            ["y" => 362, "label" => "Week1", "indexLabel" => "362" ],
            ["y" => 376, "label" => "Week2", "indexLabel" => "376" ],
            ["y" => 400, "label" => "Week3", "indexLabel" => "400" ],
            ["y" => 150, "label" => "Week4", "indexLabel" => "150" ],
            ["y" => 72, "label" => "Week5", "indexLabel" => "72" ],
            ["y" => 32, "label" => "Week6", "indexLabel" => "32" ],
            ["y" => 120, "label" => "Week7", "indexLabel" => "120"],
            ["y" => 86, "label" => "Week8", "indexLabel" => "86" ],
            ["y" => 49, "label" => "Week9", "indexLabel" => "49" ],
            ["y" => 51, "label" => "Week10", "indexLabel" => "51" ],
            ["y" => 31, "label" => "Week11", "indexLabel" => "31" ],
            ["y" => 12, "label" => "Week12", "indexLabel" => "12" ],
            ["y" => 96, "label" => "Week13", "indexLabel" => "96" ]
        ];
        return json_encode($data);
    }

    public function patientregistrationMonth(){
        $data  = [
            [ "y" => 1175, "label" => "Jul 2018", "indexLabel" => "1175" ],
            [ "y" => 1292, "label" => "Aug 2018", "indexLabel" => "1292" ],
            [ "y" => 1171, "label" => "Sep 2018", "indexLabel" => "1171" ],
            [ "y" => 1561, "label" => "Oct 2018", "indexLabel" => "1561" ],
            [ "y" => 1146, "label" => "Nov 2018", "indexLabel" => "1146" ],
            [ "y" => 1265, "label" => "Dec 2018", "indexLabel" => "1265" ],
            [ "y" => 1362, "label" => "Jan 2019", "indexLabel" => "1362" ],
            [ "y" => 1376, "label" => "Feb 2019", "indexLabel" => "1376" ],
            [ "y" => 1400, "label" => "Mar 2019", "indexLabel" => "1400" ],
            [ "y" => 1360, "label" => "Apr 2019", "indexLabel" => "1360" ],
            [ "y" => 1380, "label" => "May 2019", "indexLabel" => "1380" ],
            [ "y" => 1065, "label" => "Jun 2019", "indexLabel" => "1065" ]
        ];
        return json_encode($data);
    }

    public function patientregistrationWeekFromtoEnd() {
	    $data  = [
		    [ "y" => 1175, "label" => "Jul 2018", "indexLabel" => "1175" ],
		    [ "y" => 1292, "label" => "Aug 2018", "indexLabel" => "1292" ],
		    [ "y" => 1171, "label" => "Sep 2018", "indexLabel" => "1171" ],
		    [ "y" => 1561, "label" => "Oct 2018", "indexLabel" => "1561" ],
		    [ "y" => 1146, "label" => "Nov 2018", "indexLabel" => "1146" ],
		    [ "y" => 1265, "label" => "Dec 2018", "indexLabel" => "1265" ],
		    [ "y" => 1362, "label" => "Jan 2019", "indexLabel" => "1362" ],
		    [ "y" => 1376, "label" => "Feb 2019", "indexLabel" => "1376" ],
		    [ "y" => 1400, "label" => "Mar 2019", "indexLabel" => "1400" ],
		    [ "y" => 1360, "label" => "Apr 2019", "indexLabel" => "1360" ],
		    [ "y" => 1380, "label" => "May 2019", "indexLabel" => "1380" ],
		    [ "y" => 1065, "label" => "Jun 2019", "indexLabel" => "1065" ]
	    ];

	    return json_encode($data);
    }

    public function patientregistrationMonthYear() {
	    $data  = [
		    ["y" => 362, "label" => "Week1", "indexLabel" => "362" ],
		    ["y" => 376, "label" => "Week2", "indexLabel" => "376" ],
		    ["y" => 400, "label" => "Week3", "indexLabel" => "400" ],
		    ["y" => 150, "label" => "Week4", "indexLabel" => "150" ],
		    ["y" => 72, "label" => "Week5", "indexLabel" => "72" ],
		    ["y" => 32, "label" => "Week6", "indexLabel" => "32" ],
		    ["y" => 120, "label" => "Week7", "indexLabel" => "120"],
		    ["y" => 86, "label" => "Week8", "indexLabel" => "86" ],
		    ["y" => 49, "label" => "Week9", "indexLabel" => "49" ],
		    ["y" => 51, "label" => "Week10", "indexLabel" => "51" ],
		    ["y" => 31, "label" => "Week11", "indexLabel" => "31" ],
		    ["y" => 12, "label" => "Week12", "indexLabel" => "12" ],
		    ["y" => 96, "label" => "Week13", "indexLabel" => "96" ]
	    ];

	    return json_encode($data);
    }
    
    public function getHelper(){
        //Taking metric details from Metric
        $record = Metric::where('uid','=',Metric::$TYPE_PR)->first();
        
        $data = [
            'desc'   => $record['metric_description'],
            'cal'   => $record['metric_help'],
            
        ];
        
        return json_encode($data);
    }
    
    protected function getTitle(){
        $record = Metric::from(Metric::getTableName() .' as m')
        ->select('m.metric_name')
        ->where('m.uid', '=', Metric::$TYPE_PR )
        ->first();
        
        return $record->metric_name;
    }
}
