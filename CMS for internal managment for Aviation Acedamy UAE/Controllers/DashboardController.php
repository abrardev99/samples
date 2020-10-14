<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\StudentFinalLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function assessmentOverview(){
        $levels = [];
        $levels[] = StudentFinalLevel::where('final_level' , 6)->count();
        $levels[] = StudentFinalLevel::where('final_level' , 5)->count();
        $levels[] = StudentFinalLevel::where('final_level' , 4)->count();
        $levels[] = StudentFinalLevel::where('final_level' , '<=' , 3)->count();
        return response()->json($levels);
    }

    public function assessmentYear(){

         $data = [];
        for ($i=1; $i<=12; $i++){

            $from = date(date("Y").'-' .$i . '-01');
            $to = date(date("Y").'-' . $i .'-31');
            $data[] = StudentFinalLevel::whereBetween('created_at', [$from, $to])->get()->count();
        }

        return response()->json($data);
    }
}
