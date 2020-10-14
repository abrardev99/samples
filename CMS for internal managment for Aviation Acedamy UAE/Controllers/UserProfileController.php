<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function index(){
        return view('profile');
    }
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(Auth::user())],
        ]);

        $user = Auth::user();
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('user/photos'), $imageName);

            $user->photo = $imageName;
        }


        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        toast('Profile Updated Successfully','success');
        return redirect()->back();

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
            toast('Password Changed Successfully','success');
            return redirect()->back();
        } else {
            toast('Password Does not Match','error');
            return redirect()->back();
        }

    }
}
