<?php

namespace App\Http\Controllers;

use App\EmpJobReqAge;
use Illuminate\Http\Request;

class EmpJobReqAgeController extends Controller
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
            'age' => ['required'],
        ]);

        foreach ($request->age as $age){
            $ag = new EmpJobReqAge();
            $ag->age = $age;
            $job_post->ages()->save($ag);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpJobReqAge  $empJobReqAge
     * @return \Illuminate\Http\Response
     */
    public function show(EmpJobReqAge $empJobReqAge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpJobReqAge  $empJobReqAge
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpJobReqAge $empJobReqAge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpJobReqAge  $empJobReqAge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $job_post)
    {
        //
        $this->validate($request, [
            'age' => ['required'],
        ]);

        $job_post->ages()->delete();
        foreach ($request->age as $age){
            $ag = new EmpJobReqAge();
            $ag->age = $age;
            $job_post->ages()->save($ag);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpJobReqAge  $empJobReqAge
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpJobReqAge $empJobReqAge)
    {
        //
    }
}
