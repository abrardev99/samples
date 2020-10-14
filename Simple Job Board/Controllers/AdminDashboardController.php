<?php

namespace App\Http\Controllers;

use App\CompanyProfile;
use App\EmpPostJob;
use App\JAppointmentLetter;
use App\jobSeeker;
use App\ReferedBy;
use App\RefPayment;
use App\SentEmpResume;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    public function showProfile()
    {
        return view('admin.profile');
    }

    public function changePassword(Request $request)
    {

        $this->validate($request, [
            'oldpass' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if (Hash::check($request->oldpass, Auth::user()->password)) {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();
            $request->session()->flash('status', 'Password Changed Successfully!');
            return redirect('dashboard/admin/profile');
        } else {
            $request->session()->flash('status', 'Password Does not match!');
            return redirect('dashboard/admin/profile');
        }

    }

    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::user())],
        ]);
        $user = Auth::user();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->save();
        $request->session()->flash('status', 'Profile Updated Successfully!');
        return redirect('dashboard/admin/profile');
    }

    public function jobSeekers()
    {
        $jobseekers = User::all()->where('role', 'j');
        return view('admin.jobseekers', compact('jobseekers'));
    }

    public function jobSeekerDetails($id)
    {
        $jobseekerProfile = User::findOrFail($id);
        $jobSeeker = $jobseekerProfile->jobSeeker;
        $aptLetter = $jobseekerProfile->appointmentLatter;
        if ($jobSeeker) {
            $workinExps = $jobSeeker->workingExperiences;
            $indusSpecs = $jobSeeker->industrySpecializations;
            $languages = $jobSeeker->languages;
        }

        //        check if js uploaded appointment latter
        $isAptLtrUploaded = false;
        if (JAppointmentLetter::where('user_id', Auth::user()->id)->exists()) {
            $isAptLtrUploaded = true;
        }

//        also return, which users registered through using your reference
        $youReferredCount = ReferedBy::where('referred_by_user_id', Auth::user()->id)->count();
        $ref_link = 'https://www.mysalesjob.my/register/' . Auth::user()->ref_code;
        $refByUserId = DB::table('refered_by')->where('user_id', Auth::user()->id)->value('referred_by_user_id');

        if ($refByUserId) {
            $refByUser = \App\User::find($refByUserId);
            return view('admin.job_seeker_details', compact('jobseekerProfile', 'jobSeeker', 'workinExps', 'indusSpecs', 'languages'
                , 'youReferredCount', 'ref_link', 'refByUser', 'isAptLtrUploaded' , 'aptLetter'));

        }

        return view('admin.job_seeker_details', compact('jobseekerProfile', 'jobSeeker', 'workinExps', 'indusSpecs', 'languages'
            , 'youReferredCount', 'ref_link', 'isAptLtrUploaded' , 'aptLetter'));
    }

    public function employers()
    {
        $companies = CompanyProfile::all();
        return view('admin.employers', compact('companies'));
    }

    public function employerDetails($id)
    {
        $companyDetails = CompanyProfile::findOrFail($id);
        $employer = $companyDetails->user;
        return view('admin.employer_details', compact('employer', 'companyDetails'));
    }

    public function employerJobs($id)
    {
        $companyDetails = CompanyProfile::findOrFail($id);
        $employer = $companyDetails->user;
        $job_info = $employer->EmpJobPosts->sortByDesc('id');
        return view('admin.employer_jobs', compact('employer', 'job_info'));
    }

    public function employerJobDetails($id)
    {
//        dd($id);
        $empPostJob = EmpPostJob::findOrFail($id);
        $ages = $empPostJob->ages;
        $locations = $empPostJob->locations;
        $industrySpecs = $empPostJob->industrySpecs;
        $languages = $empPostJob->languages;
        $races = $empPostJob->races;
        $religions = $empPostJob->religions;
        $edit = false;
        return view('admin.employer_job_details', compact('empPostJob', 'ages', 'locations', 'industrySpecs', 'edit', 'languages', 'races', 'religions'));
    }

    public function deleteEmployerPostedJob($id)
    {
        $user = EmpPostJob::findOrFail($id);
        $user->delete();
        session()->flash('saved', 'Posted Job Deleted Successfully!');
        return redirect()->back();

    }

    public function matching($cmpName = null)
    {

        $empPostJobs = null;
        if ($cmpName == null or $cmpName == 'All') {
            $empPostJobs = EmpPostJob::all();
        } else {
            $empPostJobs = EmpPostJob::where('cmp_name', $cmpName)->get();
        }

//        dd($empPostJobs);
        $jobSeekers = jobSeeker::all();


        $percentage = 0;
        $JS = []; // Job seeker
        $JP = []; // Job Post
        $per = []; // percentage
        $Users = [];
        $exist = [];
        foreach ($empPostJobs as $empPostJob) {
            $ages = $empPostJob->ages;
            $locations = $empPostJob->locations;
            $languages = $empPostJob->languages;
            $races = $empPostJob->races;
            $religions = $empPostJob->religions;


            foreach ($jobSeekers as $jobSeeker) {
                $jsLanguages = $jobSeeker->languages;
                //        1
                foreach ($ages as $value) {
                    if ($value->age == $jobSeeker->age) {
                        $percentage = 8.3;
                        break;
                    }
                }
//        2
                foreach ($locations as $value) {
                    if ($value->location == $jobSeeker->pref_working_location) {
                        $percentage += 8.3;
                        break;
                    }
                }
//        3
                if (count($jsLanguages) > 0) {
                    foreach ($languages as $key => $value) {
                        if ($value->language == $jsLanguages[$key]->language) {
                            $percentage += 8.3;
                            break;
                        }
                    }
                }
//        4
                foreach ($races as $value) {
                    if ($value->race == $jobSeeker->race) {
                        $percentage += 8.3;
                        break;
                    }
                }
//        5
                foreach ($religions as $value) {
                    if ($value->religion == $jobSeeker->religion) {
                        $percentage += 8.3;
                        break;
                    }
                }
//        6
//        7
                if ($jobSeeker->expected_salary == $empPostJob->basic_salary) {
                    {
                        $percentage += 8.3;
                    }
                }
////        8
                if ($jobSeeker->working_experience == $empPostJob->experience) {
                    {
                        $percentage += 8.3;
                    }
                }
                if ($jobSeeker->gender == $empPostJob->gender or $empPostJob->gender == 'Any') {
                    {
                        $percentage += 8.3;
                    }
                }
////        9
                if ($jobSeeker->education_level == $empPostJob->education_level) {
                    {
                        $percentage += 8.3;
                    }
                }
////        10
                if ($jobSeeker->willing_to_travel_relocate == $empPostJob->travel_outstation) {
                    {
                        $percentage += 8.3;
                    }
                }
////        11
                if ($jobSeeker->own_transport == $empPostJob->posses_transport) {
                    {
                        $percentage += 8.3;
                    }
                }
////        12
                if ($jobSeeker->computer_skill == $empPostJob->computer_skill) {
                    {
                        $percentage += 8.3;
                    }
                }

                $JS[] = $jobSeeker;
                $jsUser = $jobSeeker->user;
                $Users[] = $jsUser;
                $JP[] = $empPostJob;
                $per[] = $percentage;
                $percentage = 0;

                if (SentEmpResume::where('user_id', $jsUser->id)->where('emp_post_job_id', $empPostJob->id)->exists()) {
                    $exist[] = true;
                } else {
                    $exist[] = false;
                }
            }

        }


        $cmpNames = EmpPostJob::select('cmp_name')->get();
        return view('admin.matching', compact('JS', 'JP', 'per', 'Users', 'cmpNames', 'exist'));
    }

    public function matchingFilter(Request $request)
    {

        $empPostJobs = null;
        if ($request->cmp_name == 'All') {
            $empPostJobs = EmpPostJob::all();
        } else {
            $empPostJobs = EmpPostJob::where('cmp_name', $request->cmp_name)->get();
        }
        $jobSeekers = jobSeeker::all();


        $percentage = 0;
        $JS = []; // Job seeker
        $JP = []; // Job Post
        $per = []; // percentage
        $Users = [];
        $exist = [];
        foreach ($empPostJobs as $empPostJob) {
            $ages = $empPostJob->ages;
            $locations = $empPostJob->locations;
            $languages = $empPostJob->languages;
            $races = $empPostJob->races;
            $religions = $empPostJob->religions;


            foreach ($jobSeekers as $jobSeeker) {
                $jsLanguages = $jobSeeker->languages;
                //        1
                foreach ($ages as $value) {
                    if ($value->age == $jobSeeker->age) {
                        $percentage = 8.3;
                        break;
                    }
                }
//        2
                foreach ($locations as $value) {
                    if ($value->location == $jobSeeker->pref_working_location) {
                        $percentage += 8.3;
                        break;
                    }
                }
//        3
                if (count($jsLanguages) > 0) {
                    foreach ($languages as $key => $value) {
                        if ($value->language == $jsLanguages[$key]->language) {
                            $percentage += 8.3;
                            break;
                        }
                    }
                }
//        4
                foreach ($races as $value) {
                    if ($value->race == $jobSeeker->race) {
                        $percentage += 8.3;
                        break;
                    }
                }
//        5
                foreach ($religions as $value) {
                    if ($value->religion == $jobSeeker->religion) {
                        $percentage += 8.3;
                        break;
                    }
                }
//        6
//        7
                if ($jobSeeker->expected_salary == $empPostJob->basic_salary) {
                    {
                        $percentage += 8.3;
                    }
                }
////        8
                if ($jobSeeker->working_experience == $empPostJob->experience) {
                    {
                        $percentage += 8.3;
                    }
                }
                if ($jobSeeker->gender == $empPostJob->gender or $empPostJob->gender == 'Any') {
                    {
                        $percentage += 8.3;
                    }
                }
////        9
                if ($jobSeeker->education_level == $empPostJob->education_level) {
                    {
                        $percentage += 8.3;
                    }
                }
////        10
                if ($jobSeeker->willing_to_travel_relocate == $empPostJob->travel_outstation) {
                    {
                        $percentage += 8.3;
                    }
                }
////        11
                if ($jobSeeker->own_transport == $empPostJob->posses_transport) {
                    {
                        $percentage += 8.3;
                    }
                }
////        12
                if ($jobSeeker->computer_skill == $empPostJob->computer_skill) {
                    {
                        $percentage += 8.3;
                    }
                }

                $JS[] = $jobSeeker;
                $jsUser = $jobSeeker->user;
                $Users[] = $jsUser;
                $JP[] = $empPostJob;
                $per[] = $percentage;
                $percentage = 0;

                if (SentEmpResume::where('user_id', $jsUser->id)->where('emp_post_job_id', $empPostJob->id)->exists()) {
                    $exist[] = true;
                } else {
                    $exist[] = false;
                }

            }
        }


        $cmpNames = EmpPostJob::select('cmp_name')->get();
        $cmpNameSelected = $request->cmp_name;
        return view('admin.matching', compact('JS', 'JP', 'per', 'Users', 'cmpNames', 'exist', 'cmpNameSelected'));
    }


    public function matchingDetails($jsUserId, $JPid, $percentage)
    {
        $jsUser = User::find($jsUserId);
        $JS = $jsUser->jobSeeker;
        $JP = EmpPostJob::find($JPid);

        $jsLanguages = $JS->languages;

        $ages = $JP->ages;
        $locations = $JP->locations;
        $languages = $JP->languages;
        $races = $JP->races;
        $religions = $JP->religions;

        $exist = false;

        if (SentEmpResume::where('user_id', $jsUserId)->exists() and SentEmpResume::where('emp_post_job_id', $JPid)->exists()) {
            $exist = true;
        }
        return view('admin.match_details', compact('jsUser', 'JS', 'JP', 'percentage', 'ages', 'locations', 'languages', 'races', 'religions', 'jsLanguages', 'exist'));
    }

    public function sendResumeToEmp($jsId, $jpId, $percentage)
    {
//        make new table and save job post id and js id in that table
        $user = User::find($jsId);

        $sentResume = new SentEmpResume();
        $sentResume->emp_post_job_id = $jpId;
        $sentResume->percentage = $percentage;
        $user->sentEmpResumes()->save($sentResume   );
        session()->flash('saved', 'Resume Sent to Employer Successfully!');
        return redirect('dashboard/admin/matching');

    }

    public function deleteSendResumeToEmp($jsId, $jpId)
    {
        $sentEmpResume = SentEmpResume::where('user_id', $jsId)->where('emp_post_job_id', $jpId)->first();
        $sentEmpResume->delete();
        session()->flash('saved', 'Resume Withdrawn from Employer Successfully!');
        return redirect('dashboard/admin/matching');
    }

    public function billing()
    {
        $jAptLtrs = JAppointmentLetter::all();

        foreach ($jAptLtrs as $jAptLtr) {

            $user = \App\User::find($jAptLtr->user_id);

            $paymentMade = false;
            if (\App\RefPayment::where('jappt_ltr_id', $jAptLtr->id)->exists())
                $paymentMade = true;

            $refByUserLevel1 = null;
            $refByUserLevel2 = null;

            $ref = \App\ReferedBy::where('user_id', $user->id)->get()->first();
            if (isset($ref)) {
                $refByUserLevel1 = \App\User::find($ref->referred_by_user_id);

                $ref2 = \App\ReferedBy::where('user_id', $refByUserLevel1->id)->get()->first();
                if (isset($ref2)) {
                    $refByUserLevel2 = \App\User::find($ref2->referred_by_user_id);
                }
            }

            $starterBonus = 0;
            $level1Bonus = 0;
            $level2Bonus = (5 / 100) * $jAptLtr->exact_salary;;
            if ($jAptLtr->exact_salary >= 1000 and $jAptLtr->exact_salary <= 2000) {
                $starterBonus = (5 / 100) * $jAptLtr->exact_salary;
                $level1Bonus = (5 / 100) * $jAptLtr->exact_salary;
            } else if ($jAptLtr->exact_salary >= 2000 and $jAptLtr->exact_salary <= 3000) {
                $starterBonus = (8 / 100) * $jAptLtr->exact_salary;
                $level1Bonus = (8 / 100) * $jAptLtr->exact_salary;
            } else if ($jAptLtr->exact_salary >= 3000) {
                $starterBonus = (10 / 100) * $jAptLtr->exact_salary;
                $level1Bonus = (10 / 100) * $jAptLtr->exact_salary;
            }

            $refByUserLevel1Name = null;
            $refByUserLevel1Id = null;
            if (isset($refByUserLevel1)) {
                $refByUserLevel1Id = $refByUserLevel1->id;
            } else {
                $level1Bonus = null;
            }

            $refByUserLevel2Name = null;
            $refByUserLevel2Id = null;
            if (isset($refByUserLevel2)) {
                $refByUserLevel2Id = $refByUserLevel2->id;
            } else {
                $level2Bonus = null;
            }

         if($paymentMade == false) {
             $refPayment = new  RefPaymentController();
             $refPayment->store($jAptLtr->id , $jAptLtr->exact_salary , $jAptLtr->user_id ,$jAptLtr->emp_post_job_id,  $starterBonus , $refByUserLevel1Id,
                 $level1Bonus , $refByUserLevel2Id , $level2Bonus
                 );
         }

        }

        $refPayments = RefPayment::all();

//        company billings.


        return view('admin.billing', compact('refPayments' , 'jAptLtrs'));
    }

    public function users()
    {
        $users = User::where('role', 'sa')->get();
        return view('admin.user.users', compact('users'));
    }

    public function addUserView()
    {
        return view('admin.user.userAdd');
    }

    public function addUser(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:3', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->role = 'sa';
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->ref_code = str_shuffle(substr($request->name, 0, 3) . rand(0, 100));
        $user->save();

//        send email to provided email with password ask user to change password.

        return redirect('dashboard/admin/users');
    }

    public function editUser($id)
    {
        $user = User::find($id);
        return view('admin.user.edit', compact('user'));
    }

    public function updateUser($id, Request $request)
    {
        $user = User::find($id);

        if (!empty($request->password)) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:255'],
                'mobile' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
                'password' => ['required', 'string', 'min:3', 'confirmed'],
            ]);

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->role = 'sa';
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->ref_code = str_shuffle(substr($request->name, 0, 3) . rand(0, 100));
            $user->save();
        } else {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:255'],
                'mobile' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            ]);

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->role = 'sa';
            $user->email = $request->email;
            $user->ref_code = str_shuffle(substr($request->name, 0, 3) . rand(0, 100));
            $user->save();
        }

//        send email to provided email with password ask user to change password.

        return redirect('dashboard/admin/users');
    }

    public function deleteUser($id)
    {
        User::find($id)->delete();
        return redirect('dashboard/admin/users');
    }

    public function reports()
    {
        $cmpTotal = CompanyProfile::all()->count();

        $companiesReceivedAtleastOneCand = DB::table('emp_post_jobs')
            ->join('sent_emp_resumes', 'emp_post_jobs.id', '=', 'sent_emp_resumes.emp_post_job_id')
            ->get();

        $companiesReceivedAtleastOneCand = collect($companiesReceivedAtleastOneCand);
        $companiesReceivedAtleastOneCand = $companiesReceivedAtleastOneCand->unique('emp_post_job_id');
        $companiesReceivedAtleastOneCand = count($companiesReceivedAtleastOneCand);


        $companiesReceivedZeroCand = $cmpTotal - $companiesReceivedAtleastOneCand;


        $cmpNames = EmpPostJob::select('cmp_name')->get();

        $totalCandSentResumes = DB::table('users')
            ->join('sent_emp_resumes', 'users.id', '=', 'sent_emp_resumes.user_id')
            ->count();


        $states = ['Wilayah Persekutuan', 'Perlis', 'Kedah', 'Pulau Pinang', 'Perak', 'Selangor', 'Melaka', 'Negeri Sembilan'];
        $totalCand = jobSeeker::all()->count();

        $jsLandedJobs = JAppointmentLetter::all()->count();

        return view('admin.reports.report', compact('cmpTotal', 'companiesReceivedZeroCand', 'companiesReceivedAtleastOneCand'
            , 'cmpNames', 'totalCandSentResumes', 'states', 'totalCand' , 'jsLandedJobs'
        ));
    }


    public function downloadResume($user_id){
        $jsUser = User::find($user_id);
        $jobSeeker = $jsUser->jobSeeker;
        $languages = $jobSeeker->languages;
        $indusSpecs = $jobSeeker->industrySpecializations;
        $workinExps = $jobSeeker->workingExperiences;
        return view('jobseeker.download_resume' , compact('jsUser' , 'jobSeeker' , 'languages' , 'workinExps' , 'indusSpecs'));

    }
}

