<?php

namespace App\Http\Livewire\Player;

use App\GamePoint;
use App\Jobs\RankingJob;
use App\Jobs\TeamRankingJob;
use App\Models\Admin\Game;
use App\Models\Player\Team;
use App\Models\Player\TeamGamePoint;
use App\Models\Player\TeamMembers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateTeam extends Component
{
    public $gameName;
    public $teamName;
    public $fee;
    public $invitemembers = [];
    public $test;

    public function store(){

        $this->validate([
            'teamName' => 'required',
            'gameName' => 'required',
            'fee' => 'required',
            'invitemembers' => 'required',
        ]);
        if(count($this->invitemembers) == 0){
            session()->flash('fail_msg', 'Please select members');
            return redirect()->back();
        }

        $this->playerHasPoints();
//        now create team
        $user = Auth::user();
        $team = new Team();
        $team->team_name = $this->teamName;
        $team->game_id = $this->gameName;
        $team->fee = $this->fee;
        $team->points = $this->fee;
        $team->ranking = 0;
        $user->teams()->save($team);
        if($team){
            RankingJob::dispatch($this->gameName)->delay(Carbon::now()->addSeconds(30));

//            deduct points from creator
            $gamePoint = GamePoint::where('user_id' , Auth::id())->where('game_id' , $this->gameName)->get()->first();
            $gamePoint->points = $gamePoint->points - $this->fee;
            $gamePoint->save();

//          register team creator as member
            $teamMemeber = new TeamMembers();
            $teamMemeber->member_id = $user->id;
            $teamMemeber->status = 'accepted';
            $team->teamMembers()->save($teamMemeber);

//            inviting members
            foreach ($this->invitemembers as $member){
                $teamMemeber = new TeamMembers();
                $teamMemeber->member_id = $member;
                $team->teamMembers()->save($teamMemeber);
            }

//            fire team ranking job
            TeamRankingJob::dispatch($this->gameName)->delay(Carbon::now()->addSeconds(30));

            session()->flash('success_msg_table', 'Team has been created, awaiting for players to accept request');
            return redirect('player/team/requests');
        }else{
            session()->flash('fail_msg', 'Could not create team');
        }

    }

    public function updatedFee(){
        $this->playerHasPoints();
    }

    public function playerHasPoints() {
        if($this->gameName == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }
        $point = GamePoint::where('user_id' , Auth::id())->where('game_id' , $this->gameName)->get()->first();
        $point = $point->points;
        if($this->fee > $point){
            session()->flash('fail_msg', 'Not enough points for this game. Points should be less than ' . $point);
            $this->fee = null;
        }
    }

    public function render()
    {
        $games = Game::where('type' , 2)->orWhere('type' , 3)->get();
        return view('livewire.player.create-team' , compact('games'));
    }
}
