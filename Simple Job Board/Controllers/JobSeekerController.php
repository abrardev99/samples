<?php

namespace App\Http\Controllers;

use App\jobSeeker;
use App\JWorkingExperience;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSeekerController extends Controller
{
    /**
     * Display a listing of the resource.
     *rolePrivilege
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware(['auth', 'rolePrivilege']);
    }
    public function index()
    {
        echo 'Im job seeker';
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
            'j_photo' => ['required' , 'max:5000'],
            'nric_number' => ['required', 'digits:12'],
            'race' => ['required'],
            'religion' => ['required'],
            'dob' => ['required'],
            'gender' => ['required'],
            'marital_status' => ['required'],
            'address' => ['required'],
            'telephone' => ['required'],
            'working_experience' => ['required'],
            'contact_previous_emp' => ['required'],
            'highest_qualification' => ['required'],
            'education_level' => ['required'],
            'willing_to_travel_relocate' => ['required'],
            'pref_working_location' => ['required'],
            'expected_salary' => ['required'],
            'employment_status' => ['required'],

        ]);

        if(jobSeeker::where('user_id', Auth::user()->id)->exists())
        {
            echo 'Duplicate user not allowed';
            die();
        }
        $imageName = request()->j_photo->getClientOriginalName();
        $fullImageName = uniqid() ." " .  $imageName;
        request()->j_photo->move(public_path('job_seeker_data'), $fullImageName);




        $user = Auth::user();
        $js = new jobSeeker();
//        required ones
        $js->j_photo = $fullImageName;
        $js->nric_number = $request->nric_number;
        $js->race = $request->race;
        $js->religion = $request->religion;
        $js->dob = $request->dob;
        $js->age = $request->age;
        $js->gender = $request->gender;
        $js->nationality = $request->nationality;
        $js->marital_status = $request->marital_status;
        $js->address = $request->address;
        $js->telephone = $request->telephone;
        $js->working_experience = $request->working_experience;
        $js->contact_previous_emp = $request->contact_previous_emp;
        $js->highest_qualification = $request->highest_qualification;
        $js->education_level = $request->education_level;
        $js->willing_to_travel_relocate = $request->willing_to_travel_relocate;
        $js->pref_working_location = $request->pref_working_location;
        $js->expected_salary = $request->expected_salary;
        $js->employment_status = $request->employment_status;

//        optional attributes
        $js->secondary_school = $request->secondary_school;
        $js->college_university = $request->college_university;
        $js->computer_skill = $request->computer_skill;
        $js->play_any_team_support = $request->play_any_team_support;
        $js->own_transport = $request->own_transport;
        $js->driver_license = $request->driver_license;
        $js->driver_license_type = $request->driver_license_type;
        $js->references = $request->references;
        $fullResumeName = null;
        if($request->hasFile('j_resume')){
            $resume = request()->j_resume->getClientOriginalName();
            $fullResumeName = uniqid() ." " .  $resume;
            request()->j_resume->move(public_path('job_seeker_data'), $fullResumeName);
        }
        $js->j_resume = $fullResumeName;
        $js->brief_description = $request->brief_description;
        $js->health_condition = $request->health_condition;
        $user->jobSeeker()->save($js);


//        gonna call the working experience store method here
//        $JW = new JWorkingExperienceController();
//        $JW->store($request, $js);
//        gonna call the industry specs store method here

        $IS = new JIndustrySpecializationController();
        $IS->store($request, $js);

//        $JW = new JWorkingExperienceController();
//        $JW->store($request, $js);
        $request->session()->flash('saved', 'You are almost there!');


        return redirect('jobseeker/resume/part2');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\jobSeeker  $jobSeeker
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
//        $industrySpecsValues = array('Automobile ', 'Advertising', 'Network Marketing', 'Gaming', '');
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;
        if($jobSeeker){
        $workinExps = $jobSeeker->workingExperiences;
        $indusSpecs = $jobSeeker->industrySpecializations;
        $languages = $jobSeeker->languages;
        return view('jobseeker.resume', compact('jobSeeker' , 'workinExps', 'indusSpecs', 'languages'));
        }else{

            return redirect('home');
        }

    }

    public function showResumePart2()
    {
//        $industrySpecsValues = array('Automobile ', 'Advertising', 'Network Marketing', 'Gaming', '');
        $user = Auth::user();
        $js = $user->jobSeeker;
        if($js){
        $workinExps = $js->workingExperiences;
        $languages = $js->languages;
        return view('jobseeker.resume_part2', compact('js' , 'workinExps', 'languages'));
        }else{

            return redirect('home');
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\jobSeeker  $jobSeeker
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\jobSeeker  $jobSeeker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jobSeekerId)
    {
        $this->validate($request, [
            'j_photo' => ['required'],
            'nric_number' => ['required', 'digits:12'],
            'race' => ['required'],
            'religion' => ['required'],
            'dob' => ['required'],
            'gender' => ['required'],
            'marital_status' => ['required'],
            'address' => ['required'],
            'telephone' => ['required'],
            'working_experience' => ['required'],
            'contact_previous_emp' => ['required'],
            'highest_qualification' => ['required'],
            'education_level' => ['required'],

            'willing_to_travel_relocate' => ['required'],
            'pref_working_location' => ['required'],
            'expected_salary' => ['required'],
            'employment_status' => ['required'],

        ]);


        $js = Auth::user()->jobSeeker()->find($jobSeekerId);
//
//        update only if user select new image file
        if($request->hasFile('j_photo')) {
            $imageName = request()->j_photo->getClientOriginalName();
            $fullImageName = uniqid() . " " . $imageName;
            request()->j_photo->move(public_path('job_seeker_data'), $fullImageName);
            $js->j_photo = $fullImageName;
        }

        if($request->hasFile('j_resume')) {
            $resumeName = request()->j_resume->getClientOriginalName();
            $fullResumeName = uniqid() . " " . $resumeName;
            request()->j_resume->move(public_path('job_seeker_data'), $fullResumeName);
            $js->j_resume = $fullResumeName;
        }

        $js->nric_number = $request->nric_number;
        $js->race = $request->race;
        $js->religion = $request->religion;
        $js->dob = $request->dob;
        $js->age = $request->age;
        $js->gender = $request->gender;
        $js->nationality = $request->nationality;
        $js->marital_status = $request->marital_status;
        $js->address = $request->address;
        $js->telephone = $request->telephone;
        $js->working_experience = $request->working_experience;
        $js->contact_previous_emp = $request->contact_previous_emp;
        $js->highest_qualification = $request->highest_qualification;
        $js->education_level = $request->education_level;
        $js->willing_to_travel_relocate = $request->willing_to_travel_relocate;
        $js->pref_working_location = $request->pref_working_location;
        $js->expected_salary = $request->expected_salary;
        $js->employment_status = $request->employment_status;

//        optional attributes
        $js->secondary_school = $request->secondary_school;
        $js->college_university = $request->college_university;

        $js->computer_skill = $request->computer_skill;
        $js->play_any_team_support = $request->play_any_team_support;
        $js->own_transport = $request->own_transport;
        $js->driver_license = $request->driver_license;
        $js->driver_license_type = $request->driver_license_type;
        $js->references = $request->references;
        $js->brief_description = $request->brief_description;
        $js->health_condition = $request->health_condition;
        $js->save();

        $ISController = new JIndustrySpecializationController();
        $ISController->update($request, $js);

//        $JW = new JWorkingExperienceController();
//        $JW->update($request, $js);

        $request->session()->flash('saved', 'You are almost there!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\jobSeeker  $jobSeeker
     * @return \Illuminate\Http\Response
     */
    public function destroy(jobSeeker $jobSeeker)
    {
        //
    }
}
