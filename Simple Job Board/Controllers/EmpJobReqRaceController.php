<?php

namespace App\Http\Controllers;

use App\EmpJobReqRace;
use Illuminate\Http\Request;

class EmpJobReqRaceController extends Controller
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
            'race' => ['required'],
        ]);

        foreach ($request->race as $rc){
            $lc = new EmpJobReqRace();
            $lc->race = $rc;
            $job_post->races()->save($lc);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpJobReqRace  $empJobReqRace
     * @return \Illuminate\Http\Response
     */
    public function show(EmpJobReqRace $empJobReqRace)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpJobReqRace  $empJobReqRace
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpJobReqRace $empJobReqRace)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpJobReqRace  $empJobReqRace
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpJobReqRace $empJobReqRace)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpJobReqRace  $empJobReqRace
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpJobReqRace $empJobReqRace)
    {
        //
    }
}
