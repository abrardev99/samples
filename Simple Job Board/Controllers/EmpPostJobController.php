<?php

namespace App\Http\Controllers;

use App\CompanyProfile;
use App\EmpJobReqLanguages;
use App\EmpJobReqLocation;
use App\EmpPostJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpPostJobController extends Controller
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
        $user = Auth::user();
        $job_info = $user->EmpJobPosts->sortByDesc('id');

        return view('emp.job_home', compact('job_info'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!CompanyProfile::where('user_id', Auth::user()->id)->exists()){
         return redirect('home');
        }
        $user = Auth::user();
        $com_profile = $user->companyProfile;
        return view('emp.job_post', compact('com_profile'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        'cmp_name', 'ssm_reg', 'address', 'telephone', 'person_incharge1','person_incharge2',
//        'experience', 'language', 'vacancies' , 'basic_salary',
//        'gender' , 'race' , 'religion' , 'education_level' , 'travel_outstation' , 'posses_transport',
//        'uniform_provided' , 'computer_skill'
        $this->validate($request, [
            'cmp_name' => ['required'],
            'ssm_reg' => ['required'],
            'address' => ['required'],
            'telephone' => ['required'],
            'person_incharge1' => ['required'],
            'experience' => ['required'],
            'vacancies' => ['required'],
            'basic_salary' => ['required'],
            'gender' => ['required'],
            'education_level' => ['required'],
            'travel_outstation' => ['required'],
            'posses_transport' => ['required'],
            'uniform_provided' => ['required'],
            'computer_skill' => ['required'],
            'job_pos' => ['required'],

        ]);

        $user = Auth::user();
        $job_post = new EmpPostJob();
        $job_post->cmp_name = $request->cmp_name;
        $job_post->ssm_reg = $request->ssm_reg;
        $job_post->address = $request->address;
        $job_post->telephone = $request->telephone;
        $job_post->person_incharge1 = $request->person_incharge1;
        $job_post->person_incharge2 = $request->person_incharge2;
        $job_post->experience = $request->experience;
        $job_post->vacancies = $request->vacancies;
        $job_post->basic_salary = $request->basic_salary;
        $job_post->gender = $request->gender;
        $job_post->education_level = $request->education_level;
        $job_post->travel_outstation = $request->travel_outstation;
        $job_post->posses_transport = $request->posses_transport;
        $job_post->uniform_provided = $request->uniform_provided;
        $job_post->computer_skill = $request->computer_skill;
        $job_post->job_pos = $request->job_pos;

        $user->EmpJobPosts()->save($job_post);


//        saving ages array
        $age = new EmpJobReqAgeController();
        $age->store($request, $job_post);

//        saving location array
        $locations = new EmpJobReqLocationController();
        $locations->store($request, $job_post);

        $language = new EmpJobReqLanguagesController();
        $language->store($request, $job_post);

        $race = new EmpJobReqRaceController();
        $race->store($request, $job_post);

        $religion = new EmpJobReqReligionController();
        $religion->store($request, $job_post);

//        saving industrySpecs array
        $industrySpecs = new EmpJobReqIndustrySpecsController();
        $industrySpecs->store($request, $job_post);

        $request->session()->flash('saved', 'New Job Posted Successfully!');
        return redirect('jobposting');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpPostJob  $empPostJob
     * @return \Illuminate\Http\Response
     */
    public function show($empPostJobId)
    {
        $empPostJob = EmpPostJob::findOrFail($empPostJobId);
        $ages = $empPostJob->ages;
        $locations = $empPostJob->locations;
        $indusSpecs = $empPostJob->industrySpecs;
        $languages = $empPostJob->languages;
        $races = $empPostJob->races;
        $religions = $empPostJob->religions;
        $edit = false;
        return view('emp.job_details', compact('empPostJob', 'ages', 'locations', 'indusSpecs' , 'edit' , 'languages', 'races', 'religions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpPostJob  $empPostJob
     * @return \Illuminate\Http\Response
     */
    public function edit($empPostJobId)
    {
        $empPostJob = EmpPostJob::findOrFail($empPostJobId);
        $ages = $empPostJob->ages;
        $locations = $empPostJob->locations;
        $indusSpecs = $empPostJob->industrySpecs;
        $languages = $empPostJob->languages;
        $races = $empPostJob->races;
        $religions = $empPostJob->religions;
        $edit = true;
        return view('emp.job_details', compact('empPostJob', 'ages', 'locations', 'indusSpecs' , 'edit' , 'languages', 'races', 'religions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpPostJob  $empPostJob
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $empPostJobId)
    {
        $this->validate($request, [
            'cmp_name' => ['required'],
            'ssm_reg' => ['required'],
            'address' => ['required'],
            'telephone' => ['required'],
            'person_incharge1' => ['required'],
            'experience' => ['required'],
            'vacancies' => ['required'],
            'basic_salary' => ['required'],
            'gender' => ['required'],
            'education_level' => ['required'],
            'travel_outstation' => ['required'],
            'posses_transport' => ['required'],
            'uniform_provided' => ['required'],
            'computer_skill' => ['required'],
            'job_pos' => ['required'],
        ]);

        $user = Auth::user();
        $job_post = EmpPostJob::findOrFail($empPostJobId);
        $job_post->cmp_name = $request->cmp_name;
        $job_post->ssm_reg = $request->ssm_reg;
        $job_post->address = $request->address;
        $job_post->telephone = $request->telephone;
        $job_post->person_incharge1 = $request->person_incharge1;
        $job_post->person_incharge2 = $request->person_incharge2;
        $job_post->experience = $request->experience;
        $job_post->vacancies = $request->vacancies;
        $job_post->basic_salary = $request->basic_salary;
        $job_post->gender = $request->gender;
        $job_post->education_level = $request->education_level;
        $job_post->travel_outstation = $request->travel_outstation;
        $job_post->posses_transport = $request->posses_transport;
        $job_post->uniform_provided = $request->uniform_provided;
        $job_post->computer_skill = $request->computer_skill;
        $job_post->job_pos = $request->job_pos;

        $user->EmpJobPosts()->save($job_post);

        //        saving ages array
        $age = new EmpJobReqAgeController();
        $age->update($request, $job_post);

//        saving location array
        $locations = new EmpJobReqLocationController();
        $locations->update($request, $job_post);

        $language = new EmpJobReqLanguagesController();
        $language->store($request, $job_post);

        $race = new EmpJobReqRaceController();
        $race->store($request, $job_post);

        $religion = new EmpJobReqReligionController();
        $religion->store($request, $job_post);

//        saving industrySpecs array
        $industrySpecs = new EmpJobReqIndustrySpecsController();
        $industrySpecs->update($request, $job_post);

        $request->session()->flash('saved', 'Job Post Updated Successfully!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpPostJob  $empPostJob
     * @return \Illuminate\Http\Response
     */
    public function destroy($empPostJobId )
    {
        $empPostJob = EmpPostJob::findOrFail($empPostJobId);
        $empPostJob->ages()->delete();
        $empPostJob->locations()->delete();
        $empPostJob->industrySpecs()->delete();
        $empPostJob->delete();
        return redirect('jobposting');
    }
}
