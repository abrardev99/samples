<?php

namespace App\Http\Livewire\Player;

use App\CompetitionTeamMatch;
use App\TournamentScheduleTeamMatche;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClaimArgueTeamMatch extends Component
{
    public function render()
    {
        $teamMatches = \App\TeamMatch::all();
        $tournamentTeamMatches = TournamentScheduleTeamMatche::all();
        $competitionTeamMatches = CompetitionTeamMatch::all();
        return view('livewire.player.claim-argue-team-match' ,
            compact('teamMatches' , 'tournamentTeamMatches' , 'competitionTeamMatches'));
    }
}
