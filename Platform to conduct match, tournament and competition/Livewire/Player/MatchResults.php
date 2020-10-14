<?php

namespace App\Http\Livewire\Player;

use App\GamePoint;
use App\Jobs\RankingJob;
use App\Jobs\TeamRankingJob;
use App\Models\Player\OneToOneMatch;
use App\Models\Player\Team;
use App\TeamMatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MatchResults extends Component
{
    public function lostDeleteClaim($matchId, $claimId)
    {
        $claim = \App\ClaimArgueOneToOneMatch::findOrFail($claimId);
        if($claim->is_show_to_admin == false){
            if(!$claim->delete())
            {
                session()->flash('fail_msg_table', 'Operation Failed, Please try again latter.');
                return redirect()->back();
            }

            $this->lostCommonCode($matchId);
            session()->flash('success_msg_table', 'Results  Updated.');
            return redirect()->back();
        }else{
            session()->flash('fail_msg_table', 'Time Expired or Opponent choose to LOST.');
            return redirect()->back();
        }
    }

    public function lost($matchId){

        $this->lostCommonCode($matchId);
        session()->flash('success_msg_table', 'Results  Updated.');
        return redirect()->back();

    }

    public function lostCommonCode($matchId)
    {
        $match = OneToOneMatch::findOrFail($matchId);
        //        move points to opponent
        $gamePoint = GamePoint::where('user_id' , $this->getOpponentId($match))->where('game_id' , $match->game_id)->get()->first();
        $gamePoint->points = $gamePoint->points + $match->points;
        $gamePoint->save();

        $match->status = 'completed';
        $match->winner_id = $this->getOpponentId($match);
        $match->looser_id = Auth::id();
        $match->save();

//        fire update ranking job
        RankingJob::dispatch($match->game_id)->delay(Carbon::now()->addSeconds(30));

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

//    team match methods

    public function lostTeamDeleteClaim($teamMatchId, $gameId, $claimId)
    {
        $claim = \App\ClaimArgueTeamMatch::findOrFail($claimId);
        if($claim->is_show_to_admin == false){
            if(!$claim->delete())
            {
                session()->flash('fail_msg_table', 'Operation Failed, Please try again latter.');
                return redirect()->back();
            }

            $this->lostTeamMatch($teamMatchId, $gameId);
        }else{
            session()->flash('fail_msg_table', 'Time Expired or Opponent choose to LOST.');
            return redirect()->back();
        }
    }
    public function lostTeamMatch($teamMatchId, $gameId){
        $teamMatch = TeamMatch::findOrFail($teamMatchId);
        $teamOne = $teamMatch->teamOne;
        $teamTwo = $teamMatch->teamTwo;

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

//        points are already deducted when creating match
//        move points to winner
        $winnerTeam = Team::findOrFail($opponentUserTeamId);
        $winnerTeam->points = $winnerTeam->points + $teamMatch->points;
        $winnerTeam->save();

        $teamMatch->winner_id = $opponentUserTeamId;
        $teamMatch->looser_id = $authUserTeamId;
        $teamMatch->status = 'completed';
        $teamMatch->save();

//            fire team ranking job
        TeamRankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

        session()->flash('success_msg_team_table', 'Results  Updated.');
        return redirect()->back();

    }

    public function render()
    {
        $matches = \App\Models\Player\OneToOneMatch::where('player_one_id' , Auth::id())
            ->orWhere('player_two_id' , Auth::id())->get();
        $teamMatches = TeamMatch::all();
        return view('livewire.player.match-results' , compact('matches', 'teamMatches'));
    }
}
