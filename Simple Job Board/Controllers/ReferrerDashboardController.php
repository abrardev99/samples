<?php

namespace App\Http\Controllers;

use App\JAppointmentLetter;
use App\ReferedBy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ReferrerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'isReferrer']);
    }

    public function showRef()
    {

//        also return, which users registered through using your reference
        $youReferredCount = ReferedBy::where('referred_by_user_id', Auth::user()->id)->count();
        $ref_link = 'https://www.mysalesjob.my/register/'. Auth::user()->ref_code;
        $refByUserId = DB::table('refered_by')->where('user_id', Auth::user()->id)->value('referred_by_user_id');

        if ($refByUserId) {
            $refByUser = \App\User::find($refByUserId);
            return view('ref.ref', compact('youReferredCount',  'ref_link','refByUser', 'isAptLtrUploaded'));
        }

        return view('ref.ref' , compact('youReferredCount', 'ref_link'));


    }

    public function showProfile(){
        return view('ref.profile');
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
            return redirect('referrerdashboard/profile');
        } else {
            $request->session()->flash('status', 'Password Does not match!');
            return redirect('referrerdashboard/profile');
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
        return redirect('referrerdashboard/profile');
    }
}
