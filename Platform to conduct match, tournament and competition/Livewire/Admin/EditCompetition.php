<?php

namespace App\Http\Livewire\Admin;

use App\Competition;
use Livewire\Component;

class EditCompetition extends Component
{
    public $gameId;
    public $name;
    public $levels;
    public $feeType;
    public $competitionId;

    public function mount($id){
        $this->competitionId = $id;
    }

    public function update(){
        $competition = Competition::findOrFail($this->competitionId);
        $competition->game_id = $this->gameId;
        $competition->name = $this->name;
        $competition->levels = $this->levels;
        $competition->feeType = $this->feeType;
        $competition->save();

        session()->flash('success_msg', 'Competition Updated Successfully.');
        return redirect('admin/competition/display');
    }


    public function render()
    {
        $competition = Competition::findOrFail($this->competitionId);
        $this->gameId = $competition->game->id;
        $this->name = $competition->name;
        $this->levels = $competition->levels;
        $this->feeType = $competition->feeType;
        $games = \App\Models\Admin\Game::all();
        return view('livewire.admin.edit-competition' , compact('competition' , 'games'));
    }
}
