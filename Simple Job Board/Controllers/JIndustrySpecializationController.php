<?php

namespace App\Http\Controllers;

use App\JIndustrySpecialization;
use App\jobSeeker;
use Illuminate\Http\Request;

class JIndustrySpecializationController extends Controller
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
    public function store(Request $request, $js)
    {
        $this->validate($request, [
        'industry_specialization' => ['required'],
    ]);

        foreach ($request->industry_specialization as $name){
            $IS = new JIndustrySpecialization();
            $IS->name = $name;
            $js->industrySpecializations()->save($IS);
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JIndustrySpecialization  $jIndustrySpecialization
     * @return \Illuminate\Http\Response
     */
    public function show(JIndustrySpecialization $jIndustrySpecialization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JIndustrySpecialization  $jIndustrySpecialization
     * @return \Illuminate\Http\Response
     */
    public function edit(JIndustrySpecialization $jIndustrySpecialization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JIndustrySpecialization  $jIndustrySpecialization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jobSeekr)
    {
        $this->validate($request, [
            'industry_specialization' => ['required'],
        ]);

        $this->destroy($jobSeekr->id);
        foreach ($request->industry_specialization as $name){
            $IS = new JIndustrySpecialization();
            $IS->name = $name;
            $jobSeekr->industrySpecializations()->save($IS);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JIndustrySpecialization  $jIndustrySpecialization
     * @return \Illuminate\Http\Response
     */
    public function destroy($jsId)
    {
        JIndustrySpecialization::where('job_seeker_id',$jsId)->delete();
    }
}
