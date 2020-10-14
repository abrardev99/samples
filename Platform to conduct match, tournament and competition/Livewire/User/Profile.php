<?php

namespace App\Http\Livewire\User;

use App\Countries;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
    public $name;
    public $email;
    public $city;
    public $country;
    protected $user;

    public function mount(){
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->city = $user->city;
        $this->country = $user->country;
    }

    public function updateProfile(){

        $this->validate( [
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string'],
            'country' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255',  Rule::unique('users')->ignore(Auth::user())],
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->city = $this->city;
        $user->country = $this->country;
        if($user->save()){
            session()->flash('success_msg', 'Profile successfully updated.');
        }else{
            session()->flash('success_msg', 'Profile failed updated.');
        }

    }
    public function render()
    {
        $countriesList = Countries::all();

        return view('livewire.user.profile' , compact('countriesList'));
    }
}
