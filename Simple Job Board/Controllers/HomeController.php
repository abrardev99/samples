<?php

namespace App\Http\Controllers;

use App\CompanyProfile;
use App\jobSeeker;
use App\JWorkingExperience;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
    	$user = Auth::user();
    	If($user->isJobSeeker()){

    	    if(jobSeeker::where('user_id', Auth::user()->id)->exists()){
    	    $isResumeDone = true;
    	    $jobSeeker = $user->jobSeeker;

             if(count($jobSeeker->workingExperiences) > 0  && count($jobSeeker->languages) > 0){
                 return view('jobseeker.home', compact('isResumeDone'));
             }
             else{
                 return redirect('jobseeker/resume/part2');
             }

//    	    if(JWorkingExperience::where('job_seeker_id', $js->id)){
//    	        echo 'working exp exist';
//            }

//		    return view('jobseeker.home', compact('isResumeDone'));
            }
    	    else{
                $isResumeDone = false;
                return view('jobseeker.home', compact('isResumeDone'));
            }

	    }
    	else if($user->isEmployer()){

            if(CompanyProfile::where('user_id', Auth::user()->id)->exists()){
                return redirect('jobposting');
            }
            else{
                return redirect('employer/dashboard/show/companyprofile');
            }

        }
    	else if($user->isReferrer()){
            return view('ref.home');
        }
    	else if($user->isAdmin()){
            $date = Carbon::today()->subDays(7);
            $userCount = User::where('created_at', '>=', $date)->count();

            $jobSeekers = jobSeeker::orderBy('id', 'DESC')->limit(15)->get();
            return view('admin.home' , compact('userCount' , 'jobSeekers' ));
        }
    	else if($user->isSubAdmin()){
    	    return view('sa.home');
        }
    	else{
    		Auth::logout();
    		return redirect('/login');
	    }


    }
}
