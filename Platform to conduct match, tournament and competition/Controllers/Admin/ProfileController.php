<?php

namespace App\Http\Controllers\Admin;

use App\Countries;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getProfile(){
        return view('admin.profile');
    }

}
