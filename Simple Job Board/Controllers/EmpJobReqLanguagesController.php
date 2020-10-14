<?php

namespace App\Http\Controllers;

use App\EmpJobReqLanguages;
use Illuminate\Http\Request;

class EmpJobReqLanguagesController extends Controller
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
            'language' => ['required'],
        ]);

        foreach ($request->language as $lang){
            $lc = new EmpJobReqLanguages();
            $lc->language = $lang;
            $job_post->languages()->save($lc);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpJobReqLanguages  $empJobReqLanguages
     * @return \Illuminate\Http\Response
     */
    public function show(EmpJobReqLanguages $empJobReqLanguages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpJobReqLanguages  $empJobReqLanguages
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpJobReqLanguages $empJobReqLanguages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpJobReqLanguages  $empJobReqLanguages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpJobReqLanguages $empJobReqLanguages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpJobReqLanguages  $empJobReqLanguages
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpJobReqLanguages $empJobReqLanguages)
    {
        //
    }
}
