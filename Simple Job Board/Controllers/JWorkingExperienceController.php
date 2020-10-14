<?php

namespace App\Http\Controllers;

use App\jobSeeker;
use App\JWorkingExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class JWorkingExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

//        validation need here,
        $this->validate($request, [
            'jsId' => ['required'],
            'employer' => ['required'],
            'position' => ['required'],
            'date_joined' => ['required'],
            'reporting_superior_name' => ['required'],
            'last_drawn_salary' => ['required'],
    ]);

        $js = jobSeeker::findOrFail($request->jsId);
        if($js){
            $JW = new JWorkingExperience();
            $JW->employer = $request->employer;
            $JW->position = $request->position;
            $JW->date_joined = $request->date_joined;
            if($request->has('date_left'))
            {
                $JW->date_left = $request->date_left;
            }
            $JW->reporting_superior_name = $request->reporting_superior_name;
            $JW->reason_for_leaving = $request->reason_for_leaving;
            $JW->last_drawn_salary = $request->last_drawn_salary;

            $js->workingExperiences()->save($JW);

        }
        else{
            echo 'Job Seeker not found';
            die();
        }
        $request->session()->flash('saved', 'Employer Added!');
        return redirect('jobseeker/resume/part2');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JWorkingExperience  $jWorkingExperience
     * @return \Illuminate\Http\Response
     */
    public function show(JWorkingExperience $jWorkingExperience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JWorkingExperience  $jWorkingExperience
     * @return \Illuminate\Http\Response
     */
    public function edit( $jWorkingExperienceId)
    {
        $jWorkingExperience = JWorkingExperience::findOrFail($jWorkingExperienceId);
        return view('jobseeker.edit_workingExp', compact('jWorkingExperience'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JWorkingExperience  $jWorkingExperience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $workingExpId)
    {
        $JW = JWorkingExperience::findOrFail($workingExpId);
        $JW->employer = $request->employer;
        $JW->position = $request->position;
        $JW->date_joined = $request->date_joined;
        if($request->has('date_left'))
        {
            $JW->date_left = $request->date_left;
        }
        $JW->reporting_superior_name = $request->reporting_superior_name;
        $JW->reason_for_leaving = $request->reason_for_leaving;
        $JW->last_drawn_salary = $request->last_drawn_salary;
        $JW->save();
        $request->session()->flash('saved', 'Employer Updated!');
        return redirect('jobseeker/show');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JWorkingExperience  $jWorkingExperience
     * @return \Illuminate\Http\Response
     */
    public function destroy($jsId)
    {
        JWorkingExperience::findOrFail($jsId)->delete();
        return redirect('jobseeker/show');
//        JWorkingExperience::where('job_seeker_id',$jsId)->delete();
    }

    public function delete($id){
        JWorkingExperience::findOrFail($id)->delete();
        return redirect('jobseeker/show');
    }
}
