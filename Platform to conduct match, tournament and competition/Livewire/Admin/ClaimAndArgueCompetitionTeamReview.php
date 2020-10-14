<?php

namespace App\Http\Livewire\Admin;

use App\ClaimArgueCompetitionTeamMatch;
use App\Competition;
use App\CompetitionTeamMatch;
use App\CompetitionTeamMatchCounter;
use App\CompetitionTeamPoints;
use App\Jobs\UpdateCompetitionLooserTeamLevel;
use App\Jobs\UpdateCompetitionWinnerTeamLevel;
use Livewire\Component;

class ClaimAndArgueCompetitionTeamReview extends Component
{
    public function claimerWon($claimId)
    {
        $claim = ClaimArgueCompetitionTeamMatch::findOrFail($claimId);
        $winnerTeamId = $claim->claimer_team_id;
        $matchId = $claim->competition_team_match_id;
        $looserTeamId = null;
        $match = CompetitionTeamMatch::findOrFail($claim->competition_team_match_id);
        $competitionId = $match->competition_id;

        if($match->team_one != $winnerTeamId)
            $looserTeamId = $match->team_one;

        if($match->team_two != $winnerTeamId)
            $looserTeamId = $match->team_two;

        //        give winner team points
        $competitionWinnerPoints = new CompetitionTeamPoints();
        $competitionWinnerPoints->competition_id = $competitionId;
        $competitionWinnerPoints->team_id = $winnerTeamId;
        $competitionWinnerPoints->match_id = $matchId;
        $competitionWinnerPoints->points = 3;
        $competitionWinnerPoints->save();

//        increment winner team counter
        $winnerTeamCounter = CompetitionTeamMatchCounter::where('competition_id' ,  $competitionId)->where('team_id' , $winnerTeamId)->get()->first();
        $winnerTeamCounter->counter = $winnerTeamCounter->counter + 1;
        $winnerTeamCounter->save();

        if($winnerTeamCounter->counter == 10){
//                zero counter
            $winnerTeamCounter->counter = 0;
            $winnerTeamCounter->save();
//            fire promote or degrade team job here.
            UpdateCompetitionWinnerTeamLevel::dispatch($winnerTeamId, $competitionId);

        }


//        give looser team points
        $competitionLooserPoints = new CompetitionTeamPoints();
        $competitionLooserPoints->competition_id = $competitionId;
        $competitionLooserPoints->team_id = $looserTeamId;
        $competitionLooserPoints->match_id = $matchId;
        $competitionLooserPoints->points = 0;
        $competitionLooserPoints->save();

        //        increment looser team counter
        $looserTeamCounter = CompetitionTeamMatchCounter::where('competition_id' ,  $competitionId)->where('team_id' , $looserTeamId)->get()->first();
        $looserTeamCounter->counter = $looserTeamCounter->counter + 1;
        $looserTeamCounter->save();

        if($looserTeamCounter->counter == 10){
//                zero counter
            $looserTeamCounter->counter = 0;
            $looserTeamCounter->save();
//            fire promote or degrade team job here.
            UpdateCompetitionLooserTeamLevel::dispatch($looserTeamId, $competitionId);

        }

        $match->winner_team = $winnerTeamId;
        $match->looser_team = $looserTeamId;
        $match->status = 'completed';
        $match->save();

        $claim->status = 'completed';
        $claim->save();

        session()->flash('success_msg_table' , 'Claimer Team won the match');
        return redirect()->back();
    }

    public function claimerLost($claimId)
    {
        $claim = ClaimArgueCompetitionTeamMatch::findOrFail($claimId);
        $looserTeamId = $claim->claimer_team_id;
        $matchId = $claim->competition_team_match_id;
        $winnerTeamId = null;
        $match = CompetitionTeamMatch::findOrFail($claim->competition_team_match_id);
        $competitionId = $match->competition_id;

        if($match->team_one != $looserTeamId)
            $winnerTeamId = $match->team_one;

        if($match->team_two != $looserTeamId)
            $winnerTeamId = $match->team_two;

        //        give winner team points
        $competitionWinnerPoints = new CompetitionTeamPoints();
        $competitionWinnerPoints->competition_id = $competitionId;
        $competitionWinnerPoints->team_id = $winnerTeamId;
        $competitionWinnerPoints->match_id = $matchId;
        $competitionWinnerPoints->points = 3;
        $competitionWinnerPoints->save();

//        increment winner team counter
        $winnerTeamCounter = CompetitionTeamMatchCounter::where('competition_id' ,  $competitionId)->where('team_id' , $winnerTeamId)->get()->first();
        $winnerTeamCounter->counter = $winnerTeamCounter->counter + 1;
        $winnerTeamCounter->save();

        if($winnerTeamCounter->counter == 10){
//                zero counter
            $winnerTeamCounter->counter = 0;
            $winnerTeamCounter->save();
//            fire promote or degrade team job here.
            UpdateCompetitionWinnerTeamLevel::dispatch($winnerTeamId, $competitionId);

        }


//        give looser team points
        $competitionLooserPoints = new CompetitionTeamPoints();
        $competitionLooserPoints->competition_id = $competitionId;
        $competitionLooserPoints->team_id = $looserTeamId;
        $competitionLooserPoints->match_id = $matchId;
        $competitionLooserPoints->points = 0;
        $competitionLooserPoints->save();

        //        increment looser team counter
        $looserTeamCounter = CompetitionTeamMatchCounter::where('competition_id' ,  $competitionId)->where('team_id' , $looserTeamId)->get()->first();
        $looserTeamCounter->counter = $looserTeamCounter->counter + 1;
        $looserTeamCounter->save();

        if($looserTeamCounter->counter == 10){
//                zero counter
            $looserTeamCounter->counter = 0;
            $looserTeamCounter->save();
//            fire promote or degrade team job here.
            UpdateCompetitionLooserTeamLevel::dispatch($looserTeamId, $competitionId);

        }

        $match->winner_team = $winnerTeamId;
        $match->looser_team = $looserTeamId;
        $match->status = 'completed';
        $match->save();

        $claim->status = 'completed';
        $claim->save();

        session()->flash('success_msg_table' , 'Claimer Opponent Team won the match');
        return redirect()->back();
    }

    public function render()
    {
        $competitionTeamMatches = CompetitionTeamMatch::all();
        return view('livewire.admin.claim-and-argue-competition-team-review' ,
                    compact('competitionTeamMatches'));
    }
}
