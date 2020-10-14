<?php

namespace App\Http\Controllers;

use App\EmpJobReqReligion;
use Illuminate\Http\Request;

class EmpJobReqReligionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isEmployer']);
    }

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
    public function store(Request $request, $job_post)
    {
        $this->validate($request, [
            'religion' => ['required'],
        ]);

        foreach ($request->religion as $rlgn){
            $lc = new EmpJobReqReligion();
            $lc->religion = $rlgn;
            $job_post->religions()->save($lc);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpJobReqReligion  $empJobReqReligion
     * @return \Illuminate\Http\Response
     */
    public function show(EmpJobReqReligion $empJobReqReligion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpJobReqReligion  $empJobReqReligion
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpJobReqReligion $empJobReqReligion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpJobReqReligion  $empJobReqReligion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpJobReqReligion $empJobReqReligion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpJobReqReligion  $empJobReqReligion
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpJobReqReligion $empJobReqReligion)
    {
        //
    }
}
