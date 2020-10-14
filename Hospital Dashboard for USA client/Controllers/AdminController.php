<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Patient_type;
use Session;
use App\Traits\FavouriteMetricsTrait;

class AdminController extends Controller
{
    use FavouriteMetricsTrait;
    public function index()
    {
        $patient_type = $this->getPatientType();
        return view('admin')->with(compact('patient_type'));
    }
    
    protected  function getPatientType()
    {
        $patient_type = DB::table(Patient_type::getTableName())->get();
        return $patient_type;
    }
}
