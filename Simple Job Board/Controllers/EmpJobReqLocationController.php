<?php

namespace App\Http\Controllers;

use App\EmpJobReqLocation;
use Illuminate\Http\Request;

class EmpJobReqLocationController extends Controller
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
            'location' => ['required'],
        ]);

        foreach ($request->location as $location){
            $lc = new EmpJobReqLocation();
            $lc->location = $location;
            $job_post->locations()->save($lc);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpJobReqLocation  $empJobReqLocation
     * @return \Illuminate\Http\Response
     */
    public function show(EmpJobReqLocation $empJobReqLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpJobReqLocation  $empJobReqLocation
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpJobReqLocation $empJobReqLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpJobReqLocation  $empJobReqLocation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $job_post)
    {
        $this->validate($request, [
            'location' => ['required'],
        ]);

        $job_post->locations()->delete();
        foreach ($request->location as $location){
            $lc = new EmpJobReqLocation();
            $lc->location = $location;
            $job_post->locations()->save($lc);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpJobReqLocation  $empJobReqLocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpJobReqLocation $empJobReqLocation)
    {
        //
    }
}
