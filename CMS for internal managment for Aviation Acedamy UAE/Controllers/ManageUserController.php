<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManageUserController extends Controller
{

    public function all(){
        $users = User::where('id' , '!=' , Auth::id())->get();
        return view('manage-users' , compact('users'));
    }

    public function deleteUser($id){
        $user = User::findOrFail($id);
        $user->delete();
        toast('User Deleted Successfully','success');
        return redirect()->back();
    }

    public function createUser(){
        return view('create-user');
    }

    public function storeUser(Request $request){

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $newUser = new User();
        $newUser->name = $request->name;
        $newUser->email = $request->email;
        $newUser->role = $request->role;
        $newUser->password = Hash::make($request->password);
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('user/photos'), $imageName);

            $newUser->photo = $imageName;
        }
        $newUser->save();
        toast('New User Added' , 'success');
        return redirect('admin/user/profile');
    }

    public function editUser(User $user){
        return view('update-user' , compact('user'));
    }


    public function updateUser(Request $request,  User $user){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        if ($request->hasFile('photo')) {
            $request->validate([
                'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('user/photos'), $imageName);

            $user->photo = $imageName;
        }
        $user->save();
        toast('User Updated' , 'success');
        return redirect('admin/user/profile');
    }

    public function changeUserPassword(Request $request, User $user){
        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = Hash::make($request->password);
        $user->save();
        toast('Password Changed Successfully','success');
        return redirect('admin/user/profile');
    }
}
