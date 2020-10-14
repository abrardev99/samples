<?php

namespace App\Http\Livewire\Player;

use App\CompetitionMatch;
use App\GamePoint;
use App\Jobs\RankingJob;
use App\Models\Player\OneToOneMatch;
use App\TournamentScheduleMatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClaimArgueOneToOneMatch extends Component
{
    public function render()
    {
        $matches = \App\Models\Player\OneToOneMatch::where('player_one_id' , Auth::id())
            ->orWhere('player_two_id' , Auth::id())->get();

        $tourMatches = TournamentScheduleMatch::where('player_one' , Auth::id())
            ->orWhere('player_two' , Auth::id())->get();

        $competitionMatches = CompetitionMatch::where('player_one' , Auth::id())
            ->orWhere('player_two' , Auth::id())->get();
        return view('livewire.player.claim-argue-one-to-one-match' ,
            compact('matches' , 'tourMatches' , 'competitionMatches'));
    }
}
