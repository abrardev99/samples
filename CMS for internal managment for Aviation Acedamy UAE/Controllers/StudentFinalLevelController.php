<?php

namespace App\Http\Controllers;

use App\StudentFinalLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentFinalLevelController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'student_id' => ['required'],
            'assessment_id' => ['required'],
            'final_level' => ['required' , 'numeric']
        ]);

        $level = new StudentFinalLevel();
        $level->student_id = $request->student_id;
        $level->assessment_id = $request->assessment_id;
        $level->final_level = $request->final_level;
        $level->save();

        toast('Student Final Level Saved Successfully', 'success');
        return redirect('user/students/assessment');
    }
}
