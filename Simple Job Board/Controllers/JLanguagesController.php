<?php

namespace App\Http\Controllers;

use App\JLanguages;
use App\jobSeeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JLanguagesController extends Controller
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
        $this->validate($request, [
            'jsId' => ['required'],
            'bahasa_malaysia' => ['required'],
            'english' => ['required'],
            'mandarin' => ['required'],
        ]);

        $user = Auth::user();
        $js = $user->jobSeeker;

        if(count($js->languages) > 0)
        {
//            delete all and add again
            $js->languages()->delete();

                $lang = new JLanguages();
                $lang->language = 'Bahasa Malaysia';
                $lang->priority = $request->bahasa_malaysia;
                $js->languages()->save($lang);

                $lang = new JLanguages();
                $lang->language = 'English';
                $lang->priority = $request->english;
                $js->languages()->save($lang);

                $lang = new JLanguages();
                $lang->language = 'Mandarin';
                $lang->priority = $request->mandarin;
                $js->languages()->save($lang);

            if(count($request->other) >0){
                foreach ($request->other as $key  => $other){
                    $lang = new JLanguages();
                    $lang->language = $other;
                    $lang->priority = $request->otherlevel[$key];
                    $js->languages()->save($lang);
                }}



        }
        else{
//            add newly
            $lang = new JLanguages();
            $lang->language = 'Bahasa Malaysia';
            $lang->priority = $request->bahasa_malaysia;
            $js->languages()->save($lang);

            $lang = new JLanguages();
            $lang->language = 'English';
            $lang->priority = $request->english;
            $js->languages()->save($lang);

            $lang = new JLanguages();
            $lang->language = 'Mandarin';
            $lang->priority = $request->mandarin;
            $js->languages()->save($lang);


            if(count($request->other) >0){
            foreach ($request->other as $key  => $other){
                $lang = new JLanguages();
                $lang->language = $other;
                $lang->priority = $request->otherlevel[$key];
                $js->languages()->save($lang);
            }}

        }

        $request->session()->flash('saved', 'Languages Added!');
        return redirect('jobseeker/resume/part2');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JLanguages  $jLanguages
     * @return \Illuminate\Http\Response
     */
    public function show(JLanguages $jLanguages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JLanguages  $jLanguages
     * @return \Illuminate\Http\Response
     */
    public function edit(JLanguages $jLanguages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JLanguages  $jLanguages
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JLanguages $jLanguages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JLanguages  $jLanguages
     * @return \Illuminate\Http\Response
     */
    public function destroy(JLanguages $jLanguages)
    {
        //
    }
}
