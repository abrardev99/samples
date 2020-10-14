<?php

namespace App\Http\Livewire\Player;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Player\Contacts as ContactModel;

class Contacts extends Component
{
    public $newcontact;

    public function store(){
        if($this->newcontact == 0){
            session()->flash('fail_msg', 'Please Select Contact First');
        }

        $user = Auth::user();
        $newContact = new ContactModel();
        $newContact->contact_id = $this->newcontact;
        $user->contacts()->save($newContact);

        session()->flash('success_msg', 'Contact Selected Successfully');

    }

    public function destroy($id){
        $contact = ContactModel::findOrFail($id);
        if($contact->delete())
        {
            session()->flash('success_msg_table', 'Contact Deleted Successfully.');
        }
        else{
            session()->flash('fail_msg_table', 'Deletion failed.');
        }
    }
    public function render()
    {
        $users = User::where('role' , '!=' , 'admin')->where('id' ,'!=', Auth::id())->get();
        $contacts = Auth::user()->contacts;
        return view('livewire.player.contacts' , compact('contacts' , 'users'));
    }
}
