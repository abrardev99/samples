<?php

namespace App\Http\Controllers;

use App\JAppointmentLetter;
use App\SentEmpResume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SubAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isSubAdmin']);
    }

    public function billing(){
        $jAptLtrs = JAppointmentLetter::all();
        return view('sa.billing' , compact('jAptLtrs'));
    }


    public function showProfile(){
        return view('sa.profile');
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
            return redirect('dashboard/sa/profile');
        } else {
            $request->session()->flash('status', 'Password Does not match!');
            return redirect('dashboard/sa/profile');
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
        return redirect('dashboard/sa/profile');
    }

}
