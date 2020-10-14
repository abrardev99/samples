<?php

namespace App\Http\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $curPassword;
    public $password;
    public $password_confirmation;

    public function mount(){

    }

    public function updatedCurPassword()
    {
        if(password_verify($this->curPassword, Auth::user()->password) == false) {
            session()->flash('fail_msg', 'The Password does not match with current password.');
        }

    }

    public function updated(){
        if($this->password != $this->password_confirmation){
            session()->flash('fail_msg', 'Password Confirmation does not match');
        }
    }

    public function changePassword(){
        if(password_verify($this->curPassword, Auth::user()->password) == false) {
            session()->flash('fail_msg', 'The Password does not match with current password.');
            return true;
        }
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->save();
        session()->flash('success_msg', 'Password successfully changed.');
        $this->curPassword = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        return view('livewire.user.change-password');
    }
}
