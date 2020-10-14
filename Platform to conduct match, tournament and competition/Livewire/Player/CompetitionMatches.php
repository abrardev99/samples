<?php

namespace App\Http\Livewire\Player;

use App\CompetitionMatch;
use App\CompetitionMatchPlayerPoints;
use App\CompetitionPlayerMatchCounter;
use App\CompetitionTeamMatch;
use App\CompetitionTeamMatchCounter;
use App\CompetitionTeamMatchRequest;
use App\CompetitionTeamPoints;
use App\Jobs\UpdateCompetitionLooserLevel;
use App\Jobs\UpdateCompetitionLooserTeamLevel;
use App\Jobs\UpdateCompetitionWinnerLevel;
use App\Jobs\UpdateCompetitionWinnerTeamLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CompetitionMatches extends Component
{

    public function lostDeleteClaim($matchId, $claimId)
    {
        $claim = \App\ClaimArgueOneToOneCompetitionMatch::findOrFail($claimId);
        if($claim->is_show_to_admin == false){
            if(!$claim->delete())
            {
                session()->flash('fail_msg_table', 'Operation Failed, Please try again latter.');
                return redirect()->back();
            }
            $this->lost($matchId);
        }else{
            session()->flash('fail_msg_table', 'Time Expired or Opponent choose to LOST.');
            return redirect()->back();
        }
    }

    public function lost($matchId)
    {
//        give winner points
        $match = CompetitionMatch::findOrFail($matchId);
        DB::transaction(function () use ($match) {

            $winnerId =  $this->getOpponentId($match);
            $match->winner_id = $winnerId;
            $match->looser_id = Auth::id();
            $match->status = 'completed';
            $match->save();
//            give winner points
            $competitionWinnerPoints = new CompetitionMatchPlayerPoints();
            $competitionWinnerPoints->competition_id = $match->competition_id;
            $competitionWinnerPoints->player_id = $this->getOpponentId($match);
            $competitionWinnerPoints->match_id = $match->id;
            $competitionWinnerPoints->points = 3;
            $competitionWinnerPoints->save();

//            give looser points
            $competitionLooserPoints = new CompetitionMatchPlayerPoints();
            $competitionLooserPoints->competition_id = $match->competition_id;
            $competitionLooserPoints->player_id = Auth::id();
            $competitionLooserPoints->match_id = $match->id;
            $competitionLooserPoints->points = 0;
            $competitionLooserPoints->save();

//            increment winner match counter
            $winnerPlayerCounter = CompetitionPlayerMatchCounter::where('competition_id' , $match->competition_id)->where('player_id' , $winnerId)->get()->first();
            $winnerPlayerCounter->counter = $winnerPlayerCounter->counter + 1;
            $winnerPlayerCounter->save();


            if($winnerPlayerCounter->counter == 10){
//                zero counter
                $winnerPlayerCounter->counter = 0;
                $winnerPlayerCounter->save();
//            fire promote or degrade job here.
                UpdateCompetitionWinnerLevel::dispatch($winnerId, $match->competition_id);
            }


//            increment looser match counter
            $looserPlayerCounter = CompetitionPlayerMatchCounter::where('competition_id' , $match->competition_id)->where('player_id' , Auth::id())->get()->first();
            $looserPlayerCounter->counter = $looserPlayerCounter->counter + 1;
            $looserPlayerCounter->save();

            if($looserPlayerCounter->counter == 10){
//                zero counter
              $looserPlayerCounter->counter = 0;
              $looserPlayerCounter->save();
//            fire promote or degrade job here.
              UpdateCompetitionLooserLevel::dispatch(Auth::id(), $match->competition_id);
            }
            session()->flash('success_msg_table' , 'You Lost Match');
            return redirect()->back();

        });
    }

    public function lostTeamDeleteClaim($teamMatchId, $claimId)
    {
        $claim = \App\ClaimArgueCompetitionTeamMatch::findOrFail($claimId);
        if($claim->is_show_to_admin == false){
            if(!$claim->delete())
            {
                session()->flash('fail_msg_table', 'Operation Failed, Please try again latter.');
                return redirect()->back();
            }

            $this->lostTeamMatch($teamMatchId);
        }else{
            session()->flash('fail_msg_table', 'Time Expired or Opponent choose to LOST.');
            return redirect()->back();
        }
    }

    public function lostTeamMatch($teamMatchId)
    {
        $teamMatch = CompetitionTeamMatch::findOrFail($teamMatchId);
        $competitionId = $teamMatch->competition_id;
        $teamOne = $teamMatch->teamOne;
        $teamTwo = $teamMatch->teamTwo;
        $teamOneId = $teamOne->id;
        $teamTwoId = $teamTwo->id;

        $userId = Auth::id();
        $authUserTeamId = null;
        $opponentTeamId = null;

        if($teamOne->user_id == $userId){
            $authUserTeamId = $teamOneId;
            $opponentTeamId = $teamTwoId;
        }else if($teamTwo->user_id == $userId){
            $authUserTeamId = $teamTwoId;
            $opponentTeamId = $teamOneId;
        }

//        give winner team points
        $competitionWinnerPoints = new CompetitionTeamPoints();
        $competitionWinnerPoints->competition_id = $competitionId;
        $competitionWinnerPoints->team_id = $opponentTeamId;
        $competitionWinnerPoints->match_id = $teamMatch->id;
        $competitionWinnerPoints->points = 3;
        $competitionWinnerPoints->save();

//        increment winner team counter
        $winnerTeamCounter = CompetitionTeamMatchCounter::where('competition_id' ,  $competitionId)->where('team_id' , $opponentTeamId)->get()->first();
        $winnerTeamCounter->counter = $winnerTeamCounter->counter + 1;
        $winnerTeamCounter->save();

        if($winnerTeamCounter->counter == 10){
//                zero counter
            $winnerTeamCounter->counter = 0;
            $winnerTeamCounter->save();
//            fire promote or degrade team job here.
            UpdateCompetitionWinnerTeamLevel::dispatch($opponentTeamId, $competitionId);

        }


//        give looser team points
        $competitionLooserPoints = new CompetitionTeamPoints();
        $competitionLooserPoints->competition_id = $competitionId;
        $competitionLooserPoints->team_id = $authUserTeamId;
        $competitionLooserPoints->match_id = $teamMatch->id;
        $competitionLooserPoints->points = 0;
        $competitionLooserPoints->save();

        //        increment looser team counter
        $looserTeamCounter = CompetitionTeamMatchCounter::where('competition_id' ,  $competitionId)->where('team_id' , $authUserTeamId)->get()->first();
        $looserTeamCounter->counter = $looserTeamCounter->counter + 1;
        $looserTeamCounter->save();

        if($looserTeamCounter->counter == 10){
//                zero counter
            $looserTeamCounter->counter = 0;
            $looserTeamCounter->save();
//            fire promote or degrade team job here.
            UpdateCompetitionLooserTeamLevel::dispatch($authUserTeamId, $competitionId);

        }

        $teamMatch->winner_team = $opponentTeamId;
        $teamMatch->looser_team = $authUserTeamId;
        $teamMatch->status = 'completed';
        $teamMatch->save();



        session()->flash('success_msg_team_table' , 'Your Team Lost Match');
        return redirect()->back();
    }


    private function getOpponentId($match){
        $opponentId = null;
        $player_one_id = $match->player_one;
        $player_two_id = $match->player_two;
        $authId = Auth::id();
        if($player_one_id != $authId)
            $opponentId = $player_one_id;
        else if($player_two_id != $authId)
            $opponentId = $player_two_id;

        return $opponentId;
    }

    public function render()
    {
        $userId = Auth::id();
        $competitionMatches = CompetitionMatch::where('player_one' , $userId)->orWhere('player_two' , $userId)->get();
        $competitionTeamMatches = CompetitionTeamMatch::all();
        return view('livewire.player.competition-matches' , compact('competitionMatches' , 'competitionTeamMatches'));
    }
}
