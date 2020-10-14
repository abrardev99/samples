<?php

namespace App\Http\Livewire\Admin;

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
        return view('livewire.admin.tournament-schedule-team');
    }
}
