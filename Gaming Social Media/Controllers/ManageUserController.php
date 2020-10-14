<?php

namespace App\Http\Controllers;

use App\User;

class ManageUserController extends Controller
{
    public function index()
    {
        $users = User::role('user')->with(['userQAs', 'userAvatar' , 'userProfileText'])->paginate(20);
        return view('admin.manage-users.index', compact('users'));
    }

    public function ban(User $user)
    {
        $user->ban();
        toast('User banned successfully', 'success');
        return redirect()->back();
    }

    public function unban(User $user)
    {
        $user->unban();
        toast('User Unbanned successfully', 'success');
        return redirect()->back();
    }

    public function edit(User $user)
    {
        return view('admin.manage-users.edit' , compact('user'));
    }
}
