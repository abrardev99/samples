<?php

namespace App\Http\Livewire\Admin;

use App\ClaimArgueTeamMatch;
use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use Carbon\Carbon;
use Livewire\Component;

class ClaimAndArgueReviewTeam extends Component
{
    public function claimerWon($claimId){
//        get match from claim
        $claim = ClaimArgueTeamMatch::findOrFail($claimId);
        $claimerTeam = $claim->claimerTeam;
        $claimerTeamId = $claimerTeam->id;
        $match = $claim->teamMatch;
        $teamOne = $match->teamOne;
        $teamTwo = $match->teamTwo;

        $opponentUserTeamId = null;
        if($teamOne->id != $claimerTeamId){
            $opponentUserTeamId = $teamOne->id;}
        elseif ($teamTwo->id != $claimerTeamId)
        {
            $opponentUserTeamId = $teamTwo->id;
        }

//        add points to winner team
        $claimerTeam->points = $claimerTeam->points + $match->points;
        $claimerTeam->save();

//        update match status
        $match->status = 'completed';
        $match->winner_id = $claimerTeamId;
        $match->looser_id = $opponentUserTeamId;
        $match->save();

//        update claim status
        $claim->status = 'completed';
        $claim->save();
//        dispatch team ranking job
        //        fire update ranking job
        TeamRankingJob::dispatch($match->game_id)->delay(Carbon::now()->addSeconds(90));

        session()->flash('success_msg_table', 'Claimer Won the Match');
        return redirect()->back();
    }

    public function claimerLost($claimId){
        //        get match from claim
        $claim = ClaimArgueTeamMatch::findOrFail($claimId);
        $claimerTeam = $claim->claimerTeam;
        $claimerTeamId = $claimerTeam->id;
        $match = $claim->teamMatch;
        $teamOne = $match->teamOne;
        $teamTwo = $match->teamTwo;

        $opponentUserTeamId = null;
        if($teamOne->id != $claimerTeamId){
            $opponentUserTeamId = $teamOne->id;}
        elseif ($teamTwo->id != $claimerTeamId)
        {
            $opponentUserTeamId = $teamTwo->id;
        }

//        add points to winner team which is opponent
        $opponentTeam = Team::findOrFail($opponentUserTeamId);
        $opponentTeam->points = $opponentTeam->points + 2 * $match->points;
        $opponentTeam->save();

//        deduct double points from looser,
//        because we already deducted points for match here is double
        $claimerTeam->points = $claimerTeam->points - $match->points;
        $claimerTeam->save();

//        update match status
        $match->status = 'completed';
        $match->winner_id = $opponentUserTeamId;
        $match->looser_id = $claimerTeamId;
        $match->save();

//        update claim status
        $claim->status = 'completed';
        $claim->save();
//        dispatch team ranking job
        //        fire update ranking job
        TeamRankingJob::dispatch($match->game_id)->delay(Carbon::now()->addSeconds(90));

        session()->flash('success_msg_table', 'Claimer Lost the Match');
        return redirect()->back();

    }
    public function render()
    {
        $teamMatches = \App\TeamMatch::all();
        return view('livewire.admin.claim-and-argue-review-team' , compact('teamMatches'));
    }
}
