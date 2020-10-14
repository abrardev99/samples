<?php

namespace App\Http\Livewire\Player;

use App\Tournament;
use Livewire\Component;

class TournamentSchedule extends Component
{
    public $tournament;
    public function mount($id){
        $this->tournament = Tournament::findOrFail($id);
    }
    public function render()
    {
        return view('livewire.player.tournament-schedule');
    }
}
