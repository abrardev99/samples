<?php

namespace App\Http\Controllers;

use App\CompanyProfile;
use App\SentEmpResume;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PhpParser\Node\Expr\Array_;

class EmployerDashboard extends Controller
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


    public function companyProfile(Request $request){
        $this->validate($request, [
            'cmp_name' => ['required'],
            'ssm_reg' => ['required'],
            'address' => ['required'],
            'telephone' => ['required'],
            'person_incharge1' => ['required'],
        ]);

     $user = Auth::user();
     $cmpProfile = new CompanyProfile();
     $cmpProfile->cmp_name = $request->cmp_name;
     $cmpProfile->ssm_reg = $request->ssm_reg;
     $cmpProfile->address = $request->address;
     $cmpProfile->telephone = $request->telephone;
     $cmpProfile->person_incharge1 = $request->person_incharge1;
     $cmpProfile->person_incharge2 = $request->person_incharge2;

     $user->companyProfile()->save($cmpProfile);
        $request->session()->flash('saved', 'Company profile saved!');
     return redirect('home');

    }


    public function showCompanyProfile(){
        $user = Auth::user();
        $com_profile = $user->companyProfile;

        return view('emp.company_profile', compact('com_profile'));
    }

    public function showUpdateProfile(){

        return view('emp.profile');
    }

    public function updateProfile(Request $request){
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255',  Rule::unique('users')->ignore(Auth::user())],
        ]);
        $user = Auth::user();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->save();
        $request->session()->flash('saved', 'Profile Updated Successfully!');
        return redirect('employer/dashboard/show/profile');

    }

    public function changePassword(Request $request){

        $this->validate($request, [
            'oldpass' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if(Hash::check($request->oldpass, Auth::user()->password)) {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();
            $request->session()->flash('saved', 'Password Changed Successfully!');
            return redirect('employer/dashboard/show/profile');
        } else {
            $request->session()->flash('saved', 'Password Does not match!');
            return redirect('employer/dashboard/show/profile');
        }

    }

    public function updateCompanyProfile(Request $request){
        $this->validate($request, [
            'cmp_name' => ['required'],
            'ssm_reg' => ['required'],
            'address' => ['required'],
            'telephone' => ['required'],
            'person_incharge1' => ['required'],
        ]);



        $cmpProfile = CompanyProfile::find($request->id);
        $cmpProfile->cmp_name = $request->cmp_name;
        $cmpProfile->ssm_reg = $request->ssm_reg;
        $cmpProfile->address = $request->address;
        $cmpProfile->telephone = $request->telephone;
        $cmpProfile->person_incharge1 = $request->person_incharge1;
        $cmpProfile->person_incharge2 = $request->person_incharge2;
        $cmpProfile->current_company = $request->current_company;
        $cmpProfile->save();
        $request->session()->flash('saved', 'Company profile saved!');
        return redirect('employer/dashboard/show/profile');

    }

    public function download(){
        return view('emp.download');
    }

    public function getCRC(){
        $user = Auth::user();
        $empJobs = $user->EmpJobPosts;
        return view('emp.CRC' , compact('empJobs'));
    }

    public function sentResumeDetails($id){
        $sentEmp = SentEmpResume::find($id);
        $jsUser = User::find($sentEmp->user_id);
        $jobSeeker = $jsUser->jobSeeker;
        $languages = $jobSeeker->languages;
        $indusSpecs = $jobSeeker->industrySpecializations;
        $workinExps = $jobSeeker->workingExperiences;
        return view('emp.CRC_details' , compact('jsUser' , 'jobSeeker' , 'languages' , 'workinExps' , 'indusSpecs'));
    }

    public function sentResumeDownload($id){
        $sentEmp = SentEmpResume::find($id);
        $jsUser = User::find($sentEmp->user_id);
        $jobSeeker = $jsUser->jobSeeker;
        $languages = $jobSeeker->languages;
        $indusSpecs = $jobSeeker->industrySpecializations;
        $workinExps = $jobSeeker->workingExperiences;
        return view('emp.CRC_download_resume' , compact('jsUser' , 'jobSeeker' , 'languages' , 'workinExps' , 'indusSpecs'));

    }

}
