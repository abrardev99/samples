<?php

namespace App\Http\Livewire\Player;

use App\Competition;
use App\CompetitionPlayer;
use App\CompetitionPlayerLevel;
use App\CompetitionPlayerMatchCounter;
use App\GamePoint;
use App\Jobs\RankingJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CompetitionJoin extends Component
{
    public function join($competitionId){
        $userId = Auth::id();


        $joinComp = new CompetitionPlayer();
        $joinComp->competition_id = $competitionId;
        $joinComp->player_id = $userId;
        $joinComp->save();

//        assign level 1
        $playerLevel = new CompetitionPlayerLevel();
        $playerLevel->player_id = $userId;
        $playerLevel->competition_id = $competitionId;
        $playerLevel->level = 1;
        $playerLevel->save();

//        make competition counter
        $counter = new CompetitionPlayerMatchCounter();
        $counter->competition_id = $competitionId;
        $counter->player_id = $userId;
        $counter->counter = 0;
        $counter->save();


        session()->flash('success_msg' , 'You Joined Competition Successfully');
        return redirect()->back();
    }
    public function render()
    {
        $competitions = Competition::all();
        return view('livewire.player.competition-join' , compact('competitions'));
    }
}
