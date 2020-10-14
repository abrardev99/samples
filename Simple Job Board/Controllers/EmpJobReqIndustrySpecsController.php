<?php

namespace App\Http\Controllers;

use App\EmpJobReqIndustrySpecs;
use Illuminate\Http\Request;

class EmpJobReqIndustrySpecsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth', 'isEmployer']);
    }
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
    public function store(Request $request, $job_post)
    {
        $this->validate($request, [
            'industry_specialization' => ['required'],
        ]);

        foreach ($request->industry_specialization as $name	){
            $IS = new EmpJobReqIndustrySpecs();
            $IS->name = $name;
            $job_post->industrySpecs()->save($IS);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpJobReqIndustrySpecs  $empJobReqIndustrySpecs
     * @return \Illuminate\Http\Response
     */
    public function show(EmpJobReqIndustrySpecs $empJobReqIndustrySpecs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpJobReqIndustrySpecs  $empJobReqIndustrySpecs
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpJobReqIndustrySpecs $empJobReqIndustrySpecs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpJobReqIndustrySpecs  $empJobReqIndustrySpecs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $job_post)
    {
//
        $this->validate($request, [
            'industry_specialization' => ['required'],
        ]);

        $job_post->industrySpecs()->delete();
        foreach ($request->industry_specialization as $name	){
            $IS = new EmpJobReqIndustrySpecs();
            $IS->name = $name;
            $job_post->industrySpecs()->save($IS);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpJobReqIndustrySpecs  $empJobReqIndustrySpecs
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpJobReqIndustrySpecs $empJobReqIndustrySpecs)
    {
        //
    }
}
