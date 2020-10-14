<?php

namespace App\Http\Livewire\Player;

use App\TournamentTeam;
use Livewire\Component;

class TournamentScheduleTeam extends Component
{
    public $teamTournament;
    public function mount($id){
        $this->teamTournament = TournamentTeam::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.player.tournament-schedule-team');
    }
}
