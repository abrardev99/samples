<?php

namespace App\Http\Controllers;

use App\JAppointmentLetter;
use App\ReferedBy;
use App\SentEmpResume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JAppointmentLetterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jAptLtr = JAppointmentLetter::where('user_id', Auth::user()->id)->get()->first();

        $sentResumes = SentEmpResume::where('user_id', Auth::user()->id)->get();

        return view('jobseeker.upload_earn' ,compact(  'jAptLtr', 'sentResumes'));
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
        $user = Auth::user();
        $this->validate($request, [
            'appt_ltr' => ['required'],
            'exact_salary' => ['required'],
        ]);
        $fullAptLtrName = null;
        if($request->hasFile('appt_ltr')){
            $resume = request()->appt_ltr->getClientOriginalName();
            $fullAptLtrName = uniqid() ." " .  $resume;
            request()->appt_ltr->move(public_path('job_seeker_data'), $fullAptLtrName);
        }
        $JAptLtr = new JAppointmentLetter();
        $JAptLtr->appt_ltr = $fullAptLtrName;
        $JAptLtr->exact_salary = $request->exact_salary;
        $JAptLtr->petrol = $request->petrol;
        $JAptLtr->parking = $request->parking;
        $JAptLtr->toll = $request->toll;
        $JAptLtr->car = $request->car;
        $JAptLtr->other = $request->other;
        $JAptLtr->emp_post_job_id = $request->emp_post_job_id;
        $user->appointmentLatter()->save($JAptLtr);

        $js = $user->jobSeeker;
        $js->employment_status = '3';
        $js->save();
        session()->flash('saved', 'Appointment Letter and Salary Uploaded Successfully');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JAppointmentLetter  $jAppointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function show(JAppointmentLetter $jAppointmentLetter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JAppointmentLetter  $jAppointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function edit(JAppointmentLetter $jAppointmentLetter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JAppointmentLetter  $jAppointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jAptId)
    {
        $this->validate($request, [
            'exact_salary' => ['required'],
        ]);

        $jAppointmentLetter = JAppointmentLetter::find($jAptId);
        $fullAptLtrName = null;
        if($request->hasFile('appt_ltr')){
            $resume = request()->appt_ltr->getClientOriginalName();
            $fullAptLtrName = uniqid() ." " .  $resume;
            request()->appt_ltr->move(public_path('job_seeker_data'), $fullAptLtrName);
        }
        if($fullAptLtrName == null)
        {
            $jAppointmentLetter->exact_salary = $request->exact_salary;
            $jAppointmentLetter->petrol = $request->petrol;
            $jAppointmentLetter->parking = $request->parking;
            $jAppointmentLetter->toll = $request->toll;
            $jAppointmentLetter->car = $request->car;
            $jAppointmentLetter->other = $request->other;
            $jAppointmentLetter->emp_post_job_id = $request->emp_post_job_id;
            $jAppointmentLetter->save();
        }else{
            $jAppointmentLetter->appt_ltr = $fullAptLtrName;
            $jAppointmentLetter->exact_salary = $request->exact_salary;
            $jAppointmentLetter->petrol = $request->petrol;
            $jAppointmentLetter->parking = $request->parking;
            $jAppointmentLetter->toll = $request->toll;
            $jAppointmentLetter->car = $request->car;
            $jAppointmentLetter->other = $request->other;
            $jAppointmentLetter->emp_post_job_id = $request->emp_post_job_id;
            $jAppointmentLetter->save();
        }
        session()->flash('saved', 'Appointment Letter and Salary Updates Successfully');
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JAppointmentLetter  $jAppointmentLetter
     * @return \Illuminate\Http\Response
     */
    public function destroy(JAppointmentLetter $jAppointmentLetter)
    {
        //
    }
}
