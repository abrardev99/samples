<?php

namespace App\Http\Controllers;

use App\StudentFinalLevel;
use App\Students;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        how many students in system
        $studentCount = Students::all()->count();

//        total assessments for next 3 days, same as pending but date between today and next 3 days

        $from = Carbon::today()->format('Y-m-d');
        $to =  Carbon::today()->addDays(3)->format('Y-m-d');


//        next 3 days coming assessments
        $totalAssessments = DB::table('assessments')
            ->select( 'assessments.id')
            ->leftJoin('assessment_reports','assessments.id','=','assessment_reports.assessment_id')
            ->whereNull('assessment_reports.assessment_id')
            ->whereDate('assessments.assessment_date',  '>=', $from)
            ->whereDate('assessments.assessment_date',  '<=', $to)
            ->get()
            ->count();


        //  booked assessments
        $bookedAssessments = DB::table('assessments')
            ->select( 'assessments.id')
            ->leftJoin('assessment_reports','assessments.id','=','assessment_reports.assessment_id')
            ->whereNull('assessment_reports.assessment_id')
            ->get()
            ->count();

//        coming assessments  (if report has ope or ele report)
        $pendingAssessments = DB::table('assessments')
            ->select( 'assessments.id')
            ->leftJoin('assessment_reports','assessments.id','=','assessment_reports.assessment_id')
            ->whereNotNull('assessment_reports.assessment_id')
            ->leftJoin('student_final_levels','assessments.id','=','student_final_levels.assessment_id')
            ->whereNull('student_final_levels.assessment_id')
            ->get()
            ->count();



//        finished assessments
       $finishedAssessments = DB::table('assessments')
           ->select('student_final_levels.assessment_id')
           ->join('student_final_levels', 'assessments.id', '=', 'student_final_levels.assessment_id')
           ->whereNotNull('student_final_levels.assessment_id')
           ->get()
           ->count();

//           DB::table('students')
//            ->select( 'student_final_levels.student_id')
//            ->join('student_final_levels','students.id','=','student_final_levels.student_id')
//            ->whereNotNull('student_final_levels.student_id')
//            ->get()
//            ->count();


//        how many assessments in system
        return view('home' , compact('studentCount' , 'totalAssessments'
                    , 'pendingAssessments', 'finishedAssessments' , 'bookedAssessments'));
    }
}
