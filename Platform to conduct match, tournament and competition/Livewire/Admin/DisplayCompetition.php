<?php

namespace App\Http\Livewire\Admin;

use App\Competition;
use App\CompetitionLevel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DisplayCompetition extends Component
{
    public function destroyLevel($id){
        $competition = CompetitionLevel::findOrFail($id);
        if($competition->delete())
            session()->flash('success_level_msg' , 'Competition Deleted Successfully');
        else
            session()->flash('fail_msg' , 'Deletion Failed');

        return redirect()->back();

    }
    public function render()
    {

        $levels = CompetitionLevel::all();
        $competitons = Auth::user()->competitions;
        return view('livewire.admin.display-competition' , compact('competitons' , 'levels'));
    }

    public function destroy($id){
        $competition = Competition::findOrFail($id);
        if($competition->delete())
            session()->flash('success_msg' , 'Competition Deleted Successfully');
        else
            session()->flash('fail_msg' , 'Deletion Failed');

        return redirect()->back();

    }

    public function edit($id){
        return redirect('admin/competition/edit/' . $id);
    }

    public function editLevel($id){
        return redirect('admin/competition/level/edit/' . $id);
    }
}
