<?php

namespace App\Http\Livewire\Player;

use App\TournamentScheduleMatch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClaimAndArgueTournament extends Component
{
    public function render()
    {
//        $tourMatches = TournamentScheduleMatch::where('organizer_id' , Auth::id());
        return view('livewire.player.claim-and-argue-tournament');
    }
}
