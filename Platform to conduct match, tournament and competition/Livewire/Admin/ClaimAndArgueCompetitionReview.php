<?php

namespace App\Http\Livewire\Admin;

use App\ClaimArgueOneToOneCompetitionMatch;
use App\Competition;
use App\CompetitionLevel;
use App\CompetitionMatch;
use App\CompetitionMatchPlayerPoints;
use App\CompetitionPlayerLevel;
use App\CompetitionPlayerMatchCounter;
use App\GamePoint;
use App\Jobs\RankingJob;
use App\Jobs\UpdateCompetitionLooserLevel;
use App\Jobs\UpdateCompetitionWinnerLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClaimAndArgueCompetitionReview extends Component
{
    public function claimerWon($id)
    {
        $claim = ClaimArgueOneToOneCompetitionMatch::findOrFail($id);
        DB::transaction(function () use ($claim) {

//            update claaim
            $claim->status = 'completed';
            $winnerId = $claim->claimer_id;
            $claim->save();

//            update match
            $competitionMatch = $claim->competitionMatch;
            $looserId = $this->getOpponentId($competitionMatch , $winnerId);
            $competitionMatch->status = 'completed';
            $competitionMatch->winner_id = $winnerId;
            $competitionMatch->looser_id = $looserId;
            $competitionMatch->save();

//            give points to winner
            $competitionPoints = new CompetitionMatchPlayerPoints();
            $competitionPoints->competition_id = $competitionMatch->competition_id;
            $competitionPoints->player_id = $winnerId;
            $competitionPoints->match_id = $competitionMatch->id;
            $competitionPoints->points = 3;
            $competitionPoints->save();


//            increment winner match counter
            $winnerPlayerCounter = CompetitionPlayerMatchCounter::where('competition_id' , $competitionMatch->competition_id)->where('player_id' , $winnerId)->get()->first();
            $winnerPlayerCounter->counter = $winnerPlayerCounter->counter + 1;
            $winnerPlayerCounter->save();


            if($winnerPlayerCounter->counter == 10){
//                zero counter
                $winnerPlayerCounter->counter = 0;
                $winnerPlayerCounter->save();
//            fire promote or degrade job here.
                UpdateCompetitionWinnerLevel::dispatch($winnerId, $competitionMatch->competition_id);
            }


//            increment looser match counter
            $looserPlayerCounter = CompetitionPlayerMatchCounter::where('competition_id' , $competitionMatch->competition_id)->where('player_id' , $looserId)->get()->first();
            $looserPlayerCounter->counter = $looserPlayerCounter->counter + 1;
            $looserPlayerCounter->save();

            if($looserPlayerCounter->counter == 10){
//                zero counter
                $looserPlayerCounter->counter = 0;
                $looserPlayerCounter->save();
//            fire promote or degrade job here.
                UpdateCompetitionLooserLevel::dispatch($looserId, $competitionMatch->competition_id);
            }


        });

        session()->flash('success_msg_table' , 'Claimer won the Match');
        return redirect()->back();
    }

    public function claimerLost($id)
    {
        $claim = ClaimArgueOneToOneCompetitionMatch::findOrFail($id);
        DB::transaction(function () use ($claim) {

//            update claaim
            $claim->status = 'completed';
            $looserId = $claim->claimer_id;
            $claim->save();

//            update match
            $competitionMatch = $claim->competitionMatch;
            $winnerId = $this->getOpponentId($competitionMatch , $looserId);
            $competitionMatch->status = 'completed';
            $competitionMatch->winner_id = $winnerId;
            $competitionMatch->looser_id = $looserId;
            $competitionMatch->save();

//            give points to winner
            $competitionPoints = new CompetitionMatchPlayerPoints();
            $competitionPoints->competition_id = $competitionMatch->competition_id;
            $competitionPoints->player_id = $winnerId;
            $competitionPoints->match_id = $competitionMatch->id;
            $competitionPoints->points = 3;
            $competitionPoints->save();


//            increment winner match counter
            $winnerPlayerCounter = CompetitionPlayerMatchCounter::where('competition_id' , $competitionMatch->competition_id)->where('player_id' , $winnerId)->get()->first();
            $winnerPlayerCounter->counter = $winnerPlayerCounter->counter + 1;
            $winnerPlayerCounter->save();


            if($winnerPlayerCounter->counter == 10){
//                zero counter
                $winnerPlayerCounter->counter = 0;
                $winnerPlayerCounter->save();
//            fire promote or degrade job here.
                UpdateCompetitionWinnerLevel::dispatch($winnerId, $competitionMatch->competition_id);
            }

//            give looser points
            $competitionLooserPoints = new CompetitionMatchPlayerPoints();
            $competitionLooserPoints->competition_id = $competitionMatch->competition_id;
            $competitionLooserPoints->player_id = $looserId;
            $competitionLooserPoints->match_id = $competitionMatch->id;
            $competitionLooserPoints->points = 0;
            $competitionLooserPoints->save();


//            increment looser match counter
            $looserPlayerCounter = CompetitionPlayerMatchCounter::where('competition_id' , $competitionMatch->competition_id)->where('player_id' , $looserId)->get()->first();
            $looserPlayerCounter->counter = $looserPlayerCounter->counter + 1;
            $looserPlayerCounter->save();

            if($looserPlayerCounter->counter == 10){
//                zero counter
                $looserPlayerCounter->counter = 0;
                $looserPlayerCounter->save();
//            fire promote or degrade job here.
                UpdateCompetitionLooserLevel::dispatch($looserId, $competitionMatch->competition_id);
            }

            $competition = Competition::findOrFail($competitionMatch->competition_id);
            $competitionFeeType = $competition->feeType;
            $gameId = $competition->game_id;

            if ($competitionFeeType == '10/match') {

                //        check level, then charge fee from this level.
                $compPlayerLevel = CompetitionPlayerLevel::where('competition_id', $competitionMatch->competition_id)->where('player_id', $looserId)->get()->first();
                $compLevel = CompetitionLevel::where('competition_id', $competitionMatch->competition_id)->where('level', $compPlayerLevel->level)
                    ->where('match_type', 'player')->get()->first();
                $requiredPoints = $compLevel->fee;

                $senderGamePoints = GamePoint::where('user_id', $looserId)->where('game_id', $gameId)->get()->first();
                $senderPoints = $senderGamePoints->points;

                $senderGamePoints->points = $senderPoints - $requiredPoints;
                $senderGamePoints->save();

//        fire update ranking job
                RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

            }

            if ($competitionFeeType == 'unlimited') {
                $requiredPoints = $competition->fee;
                $senderGamePoints = GamePoint::where('user_id', $looserId)->where('game_id', $gameId)->get()->first();
                $senderPoints = $senderGamePoints->points;


                $senderGamePoints->points = $senderPoints - $requiredPoints;
                $senderGamePoints->save();

//        fire update ranking job
                RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

            }

        });

        session()->flash('success_msg_table' , 'Claimer Lost the Match');
        return redirect()->back();
    }

    private function getOpponentId($match , $winnerId){
        $opponentId = null;
        $player_one_id = $match->player_one;
        $player_two_id = $match->player_two;
        if($player_one_id != $winnerId)
            $opponentId = $player_one_id;
        else if($player_two_id != $winnerId)
            $opponentId = $player_two_id;

        return $opponentId;
    }

    public function render()
    {
        $matches = CompetitionMatch::all();

        return view('livewire.admin.claim-and-argue-competition-review' , compact('matches'));
    }
}
