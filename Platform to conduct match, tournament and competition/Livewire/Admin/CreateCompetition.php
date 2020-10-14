<?php

namespace App\Http\Livewire\Admin;

use App\Competition;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateCompetition extends Component
{
    public $gameId;
    public $name;
    public $levels;
    public $fee;
    public $feeType = 'unlimited';

    public function store(){
        if($this->gameId == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        $this->validate([
            'gameId' => 'required',
            'name' => 'required',
            'levels' => ['required', 'numeric'],
            'feeType' => ['required'],
        ]);

        $competition = new Competition();
        $competition->user_id = Auth::id();
        $competition->game_id = $this->gameId;
        $competition->name = $this->name;
        $competition->levels = $this->levels;
        $competition->feeType = $this->feeType;
        if($this->feeType == 'unlimited'){
            $competition->fee = $this->fee;
        }
        $competition->save();

        session()->flash('success_msg', 'Competition Created Successfully.');
        return redirect('admin/competition/display');
    }

    public function render()
    {
        $games = \App\Models\Admin\Game::all();
        return view('livewire.admin.create-competition' , compact('games'));
    }
}
