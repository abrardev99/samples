<?php

namespace App\Http\Livewire\Player;

use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use App\TournamentJoinedTeam;
use App\TournamentTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinTeamTournament extends Component
{
    public $tournament = null;
    public $teams = null;
    public $teamId;

    public function mount($id)
    {
        $this->tournament = TournamentTeam::findOrFail($id);
        $this->teams = Team::where('user_id' , Auth::id())->where('game_id' , $this->tournament->game_id)->get();

    }

    public function store(){
        if($this->teamId == 0){
            session()->flash('fail_msg_table', 'Please Select Team First');
            return redirect()->back();
        }

        $team = Team::findOrFail($this->teamId);
        $teamPoints = $team->points;
        $requiredPoints = $this->tournament->points;
        if(isset($team) and $teamPoints < $requiredPoints){
            session()->flash('fail_msg_table', 'Your team don\'t have enough points to join this tournament.');
            return redirect()->back();
        }

        $team->points = $teamPoints - $requiredPoints;
        $team->save();
        TeamRankingJob::dispatch($this->tournament->game_id)->delay(Carbon::now()->addSeconds(30));

        $joinedTeam = new TournamentJoinedTeam();
        $joinedTeam->team_id = $this->teamId;
        $joinedTeam->tournament_team_id = $this->tournament->id;
        $joinedTeam->save();

        session()->flash('success_msg_team_table', 'You Joined Tournament.');
        return redirect('player/tournaments');
    }

    public function render()
    {
        return view('livewire.player.join-team-tournament');
    }
}
