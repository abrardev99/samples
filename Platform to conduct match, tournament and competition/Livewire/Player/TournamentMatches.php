<?php

namespace App\Http\Livewire\Player;

use App\TournamentScheduleMatch;
use App\TournamentScheduleTeamMatche;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TournamentMatches extends Component
{
    public function lostDeleteClaim($id, $claimId)
    {
        $claim = \App\ClaimArgueOneToOneMatchTournament::findOrFail($claimId);
        if($claim->is_show_to_admin == false){
            if(!$claim->delete())
            {
                session()->flash('fail_msg_table', 'Operation Failed, Please try again latter.');
                return redirect()->back();
            }
            $this->lost($id);
         }else{
            session()->flash('fail_msg_table', 'Time Expired or Opponent choose to LOST.');
            return redirect()->back();
        }
    }
    public function lost($id){
        $match = TournamentScheduleMatch::findOrFail($id);

        $match->status = 'completed';
        $match->winner_id = $this->getOpponentId($match);
        $match->looser_id = Auth::id();
        $match->save();

        session()->flash('success_msg_table', 'Results  Updated.');
        return redirect()->back();
    }


    public function lostTeamDeleteClaim($id, $claimId)
    {
        $claim = \App\ClaimArgueTeamMatchTournament::findOrFail($claimId);
        if($claim->is_show_to_admin == false){
            if(!$claim->delete())
            {
                session()->flash('fail_msg_table', 'Operation Failed, Please try again latter.');
                return redirect()->back();
            }

            $this->lostTeamMatch($id);
        }else{
            session()->flash('fail_msg_table', 'Time Expired or Opponent choose to LOST.');
            return redirect()->back();
        }
    }

    public function lostTeamMatch($id){
        $match = TournamentScheduleTeamMatche::findOrFail($id);
        $teamOne = $match->teamOne;
        $teamTwo = $match->teamTwo;

//        know user and opponent team
        $authUserTeamId = null;
        $opponentUserTeamId = null;
        $userId = Auth::id();
        if($teamOne->user_id == $userId){
            $authUserTeamId = $teamOne->id;
            $opponentUserTeamId = $teamTwo->id;}
        elseif ($teamTwo->user_id == $userId)
        {
            $authUserTeamId = $teamTwo->id;
            $opponentUserTeamId = $teamOne->id;
        }

        $match->winner_id = $opponentUserTeamId;
        $match->looser_id = $authUserTeamId;
        $match->status = 'completed';
        $match->save();

        session()->flash('success_msg_team_table', 'Team Match Results Updated.');
        return redirect()->back();

    }

    public function getOpponentId($match){
        $looser_id = null;
        $player_one_id = $match->player_one_id;
        $player_two_id = $match->player_two_id;
        $authId = Auth::id();
        if($player_one_id != $authId)
            $looser_id = $player_one_id;
        else if($player_two_id != $authId)
            $looser_id = $player_two_id;

        return $looser_id;
    }

    public function render()
    {
        $userId = Auth::id();
        $matches = TournamentScheduleMatch::where('player_one' , $userId)
                    ->orWhere('player_two' , $userId)->get();

        $teamMatches = TournamentScheduleTeamMatche::all();
        return view('livewire.player.tournament-matches' , compact('matches' , 'teamMatches'));
    }
}
