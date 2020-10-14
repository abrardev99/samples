<?php

namespace App\Http\Livewire\Player;

use App\GamePoint;
use App\Models\Admin\Game;
use App\Models\Player\MatchRequests;
use App\Models\Player\Team;
use App\Models\Player\TeamGamePoint;
use App\TeamMatchRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateMatch extends Component
{
    public $gameid;
    public $datetime;
    public $points;
    public $gamePoints;
    public $opponent;
    public $matchMessage;
    public $matchType = 1;

//    for team option
    public $teams = null;
    public $teamId;

    public function updatedGameId(){
        if($this->gameid == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        $userId = Auth::id();
        if($this->matchType == 1)
         {
             $this->gamePoints = \App\GamePoint::where('user_id' , $userId)->where('game_id' , $this->gameid)->get()->first();
         }
        else
        {
            $this->teams = Team::where('user_id' , $userId)->where('game_id' , $this->gameid)->get();
        }
    }

//    team function
        public function updatedTeamId(){
            if($this->teamId == 0){
                session()->flash('fail_msg', 'Please Select Team First');
                return redirect()->back();
            }
            $this->gamePoints = Team::findOrFail($this->teamId);
        }

    public function store(){
        $this->validation();

        $user = Auth::user();
//        we are pretending actual points in match table but not deducting yet.
//        we will deduct/add when player loss/win match
        $userPoints = GamePoint::where('user_id' , $user->id)->where('game_id' , $this->gameid)->get()->first();
        $actualPoints = $userPoints->points - $this->points;

        $matchRequest = new MatchRequests();
        $matchRequest->opponent_id = $this->opponent;
        $matchRequest->game_id = $this->gameid;
        $matchRequest->datetime = Carbon::parse($this->datetime)->toDateTimeString();
        $matchRequest->points = $this->points;
        $matchRequest->actual_points = $actualPoints;
        $matchRequest->message = $this->matchMessage;
        $user->matchRequests()->save($matchRequest);
        session()->flash('success_msg_table', 'Match Request Created Successfully.');
        return redirect('player/match/all');
    }

    public function storeTeam(){
        $this->validation();

        $team = Team::findOrFail($this->teamId);
        $actualPoints = $team->points - $this->points;

        $teamMatchRequest = new TeamMatchRequest();
        $teamMatchRequest->opponent_team_id = $this->opponent;
        $teamMatchRequest->datetime = Carbon::parse($this->datetime)->toDateTimeString();
        $teamMatchRequest->points = $this->points;
        $teamMatchRequest->actual_points = $actualPoints;
        $teamMatchRequest->message = $this->matchMessage;
        $team->teamMatchRequests()->save($teamMatchRequest);
        session()->flash('success_msg_table', 'Team Match Request Created Successfully.');
        return redirect('player/match/all');


    }

    public function validation(){
        if($this->gameid == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        if($this->points == 0){
            session()->flash('fail_msg', 'Please Select Points First');
            return redirect()->back();
        }

        if($this->opponent == 0){
            session()->flash('fail_msg', 'Please Select Opponent First');
            return redirect()->back();
        }

        $this->validate([
            'gameid' => 'required',
            'opponent' => 'required',
            'points' => 'required',
            'datetime' => 'required',
        ]);
    }

    public function render()
    {
//        1-1 match code
        $userId = Auth::id();
//        opponent can be player contact for now, have to discuss it with martin
        $favGames = \App\FavGames::where('game_id' , $this->gameid)->where('user_id' , '!=' , $userId)->get();
//        points
        $pointsGreaterOne = \App\GamePoint::where('user_id' , $userId)->where('points' , '>' , 1)->get();

//        team match code
        $gameTeams = Team::where('game_id' , $this->gameid)->where('user_id' , '!=', $userId)->get();
        return view('livewire.player.create-match' , compact('pointsGreaterOne' , 'favGames' , 'gameTeams'));
    }
}
