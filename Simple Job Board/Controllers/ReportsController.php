<?php

namespace App\Http\Controllers;

use App\CompanyProfile;
use App\JAppointmentLetter;
use App\jobSeeker;
use App\SentEmpResume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ReportsController extends Controller
{
    public function showAllCompanies(){
        $companies = CompanyProfile::all();
        return view('admin.reports.show_all_companies' , compact('companies'));
    }

    public function showAllCompaniesNotReceivedCand(){

        $companiesReceivedZeroCand = DB::table('emp_post_jobs')
            ->leftJoin('sent_emp_resumes', 'emp_post_jobs.id', '=', 'sent_emp_resumes.emp_post_job_id')
            ->where('sent_emp_resumes.id' ,null)
            ->select('emp_post_jobs.*')
            ->get();

        return view('admin.reports.show_all_companies_not_received_cand' , compact('companiesReceivedZeroCand'));
    }

    public function showAllCompaniesReceivedAtleasetOneCand(){
        $companiesReceivedAtleastOneCand = DB::table('emp_post_jobs')
            ->join('sent_emp_resumes', 'emp_post_jobs.id', '=', 'sent_emp_resumes.emp_post_job_id')
            ->get();

        $companiesReceivedAtleastOneCand = collect($companiesReceivedAtleastOneCand);
        $companiesReceivedAtleastOneCand = $companiesReceivedAtleastOneCand->unique('emp_post_job_id');

        return view('admin.reports.show_all_companies_atleastone_received_cand' , compact('companiesReceivedAtleastOneCand'));
    }

    public function companySentCandidate(Request $request){
        $totalCandSentResumes = null;
        if($request->cmp_name == 'All'){
            $totalCandSentResumes = DB::table('users')
                ->join('sent_emp_resumes', 'users.id', '=', 'sent_emp_resumes.user_id')
                ->count();
        }
        else{
            $totalCandSentResumes = DB::table('sent_emp_resumes')
                ->join('users', 'users.id', '=', 'sent_emp_resumes.user_id')
                ->join('emp_post_jobs', 'emp_post_jobs.id', '=', 'sent_emp_resumes.emp_post_job_id')
                ->where('emp_post_jobs.cmp_name' , '=' , $request->cmp_name)
                ->count();
        }

        $cmpNameSelected = $request->cmp_name;
        return Redirect::route('dashboard/admin/reports')->with(['totalCandSentResumes' => $totalCandSentResumes, 'cmpNameSelected' => $cmpNameSelected]);
    }

    public function candByState(Request $request){
        $totalCand = null;
        if($request->state == 'All'){
            $totalCand = jobSeeker::all()->count();
        }
        else{
            $totalCand = jobSeeker::where('pref_working_location' , $request->state)->count();
        }


        $selectedState = $request->state;
        return Redirect::route('dashboard/admin/reports')->with(['totalCand' => $totalCand, 'selectedState' => $selectedState]);


    }

    public function jsLandedJobs(){
        $jsAptLtrs = JAppointmentLetter::all();
        return view('admin.reports.js_landed_jobs' , compact('jsAptLtrs'));
    }
}
