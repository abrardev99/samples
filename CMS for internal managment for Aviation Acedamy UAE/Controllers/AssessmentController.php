<?php

namespace App\Http\Controllers;

use App\Assessment;
use App\StudentFinalLevel;
use App\Students;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{

    public function index()
    {
        $assessments = Assessment::all();
        return view('assessment', compact('assessments'));
    }


    public function create()
    {
        $students = Students::all();
        return view('create-assessment', compact('students'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'location' => ['required'],
            'assessment_date' => ['required'],
            'student_id' => ['required'],
        ]);

        $user = Auth::user();

        $assessment = new Assessment();
        $assessment->student_id = $request->student_id;
        $assessment->assessor_id = $user->id;
        $assessment->location = $request->location;
        $assessment->assessment_date = Carbon::parse($request->assessment_date)->toDateString();
        $assessment->save();

        toast('Assessment Created Successfully', 'success');
        return redirect('user/student/assessment/task/create/' . $assessment->id);


    }

    public function show(Assessment $assessment)
    {
        $student = $assessment->student;
        $finalLevel = StudentFinalLevel::where('student_id' , $student->id)->where('assessment_id' , $assessment->id)->exists();
        if(!$finalLevel){
        if(\App\AssessmentReport::where('assessment_id' , $assessment->id)->where('title' , 'OPE')->exists()
         and \App\AssessmentReport::where('assessment_id' , $assessment->id)->where('title' , 'ELE')->exists()){
            $opeAssessment = \App\AssessmentReport::where('assessment_id' , $assessment->id)->where('title' , 'OPE')->get()->first();
            $eleAssessment = \App\AssessmentReport::where('assessment_id' , $assessment->id)->where('title' , 'ELE')->get()->first();
            if($opeAssessment->report_level == $eleAssessment->report_level){
//                save student final level
                $level = new StudentFinalLevel();
                $level->student_id = $student->id;
                $level->assessment_id = $assessment->id;
                $level->final_level = $opeAssessment->report_level;
                $level->save();
            }
        }
        }


            return view('assessment-details' , compact('assessment'));
    }

     public function edit(Assessment $assessment)
    {
        $students = Students::all();
        return view('update-assessment' , compact('assessment', 'students'));
    }


    public function update(Request $request, Assessment $assessment)
    {
        $request->validate([
            'location' => ['required'],
            'assessment_date' => ['required'],
            'student_id' => ['required'],
        ]);

        $assessment->student_id = $request->student_id;
        $assessment->location = $request->location;
        $assessment->assessment_date = Carbon::parse($request->assessment_date)->toDateString();
        $assessment->save();

        toast('Assessment Update Successfully' , 'success');
        return redirect('user/student/assessment/details/' . $assessment->student_id);

    }
    public function destroy(Assessment $assessment)
    {
        if($assessment->delete()){
            toast('Assessment Deleted Successfully' , 'success');
            return redirect()->back();
        }else{
            toast('Unable to Delete Assessment' , 'error');
            return redirect()->back();
        }
    }
}
