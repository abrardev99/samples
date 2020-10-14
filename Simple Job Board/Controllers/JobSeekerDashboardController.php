<?php

namespace App\Http\Controllers;

use App\JAppointmentLetter;
use App\jobSeeker;
use App\ReferedBy;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class JobSeekerDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'rolePrivilege']);
    }
    public function showProfile(){
            return view('jobseeker.profile');
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
            $request->session()->flash('status', 'Password Changed Successfully!');
            return redirect('dashboard/profile');
        } else {
            $request->session()->flash('status', 'Password Does not match!');
            return redirect('dashboard/profile');
        }

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
        $request->session()->flash('status', 'Profile Updated Successfully!');
        return redirect('dashboard/profile');
    }


    public function showRef()
    {
//        check if js uploaded appointment latter
        $isAptLtrUploaded = false;
        if(JAppointmentLetter::where('user_id', Auth::user()->id)->exists()){
            $isAptLtrUploaded = true;
        }

//        also return, which users registered through using your reference
        $youReferredCount = ReferedBy::where('referred_by_user_id', Auth::user()->id)->count();
        $ref_link = 'https://www.mysalesjob.my/register/'. Auth::user()->ref_code;
        $refByUserId = DB::table('refered_by')->where('user_id', Auth::user()->id)->value('referred_by_user_id');

        if ($refByUserId) {
            $refByUser = \App\User::find($refByUserId);
            return view('jobseeker.ref', compact('youReferredCount',  'ref_link','refByUser', 'isAptLtrUploaded'));
        }

        return view('jobseeker.ref' , compact('youReferredCount', 'ref_link', 'isAptLtrUploaded'));


    }

    public function showStatus(){
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;
            return view('jobseeker.status' , compact('jobSeeker'));
    }


    public function updateStatus(Request $request, $jobSeekerId){
        $this->validate($request, [
            'employment_status' => ['required'],
        ]);

        $jobSeeker = jobSeeker::findOrFail($jobSeekerId);
        $jobSeeker->employment_status = $request->employment_status;
        $jobSeeker->save();

        $request->session()->flash('updated', 'Status Updated Successfully!');
        return redirect('dashboard/status');
//        echo $request->employment_status;
    }

    public function download(){
        return view('jobseeker.download');
    }

    public function downloadResume(){
        $jsUser = Auth::user();
        $jobSeeker = $jsUser->jobSeeker;
        $languages = $jobSeeker->languages;
        $indusSpecs = $jobSeeker->industrySpecializations;
        $workinExps = $jobSeeker->workingExperiences;
        return view('jobseeker.download_resume' , compact('jsUser' , 'jobSeeker' , 'languages' , 'workinExps' , 'indusSpecs'));

    }

}
