<?php

namespace App\Http\Livewire\Player;

use App\Competition;
use App\CompetitionLevel;
use App\CompetitionMatch;
use App\CompetitionMatchRequest;
use App\CompetitionPlayerLevel;
use App\CompetitionPlayerMatchCounter;
use App\CompetitionPlayerUnlimtedFeePaid;
use App\CompetitionTeamMatchRequest;
use App\GamePoint;
use App\Jobs\RankingJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CompetitionMatchRequests extends Component
{
    public function accept($receivedMatchRequestId)
    {
        $matchRequest = CompetitionMatchRequest::findOrFail($receivedMatchRequestId);
        if($matchRequest->status == 'accepted'){
            session()->flash('fail_received_msg' , 'Request Accepted by someone else');
            return redirect()->back();
        }

        $user = Auth::user();
        $userId = $user->id;

        DB::transaction(function() use ($userId, $matchRequest) {

            $competitionId = $matchRequest->competition_id;

            $competition = Competition::findOrFail($competitionId);
            $competitionFeeType = $competition->feeType;
            $gameId = $competition->game_id;

//        check fee type in competition
            if ($competitionFeeType == '10/match') {

//                check if initial data saved if not then save for once only

//            check counter because its 10/match
                $matchCounter = CompetitionPlayerMatchCounter::where('competition_id', $competitionId)->where('player_id', $userId)->get()->first();

                if ($matchCounter->counter == 0) {
                    //        check level, then charge fee from this level.
                    $compPlayerLevel = CompetitionPlayerLevel::where('competition_id', $competitionId)->where('player_id', $userId)->get()->first();
                    $compLevel = CompetitionLevel::where('competition_id', $competitionId)->where('level', $compPlayerLevel->level)
                        ->where('match_type' , 'player')->get()->first();
                    $requiredPoints = $compLevel->fee;

                    $senderGamePoints = GamePoint::where('user_id', $userId)->where('game_id', $gameId)->get()->first();
                    $senderPoints = $senderGamePoints->points;

                    if (!$senderPoints or $senderPoints < $requiredPoints) {
                        session()->flash('fail_msg', 'Sender does not have enough points.');
                        return redirect()->back();
                    }

                    $senderGamePoints->points = $senderPoints - $requiredPoints;
                    $senderGamePoints->save();

//        fire update ranking job
                    RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

                }

            }
            if ($competitionFeeType == 'unlimited') {
                //                check if player paid already
                if (!CompetitionPlayerUnlimtedFeePaid::where('competition_id', $competitionId)->where('player_id', $userId)->exists()) {
                    $requiredPoints = $competition->fee;
                    $senderGamePoints = GamePoint::where('user_id', $userId)->where('game_id', $gameId)->get()->first();
                    $senderPoints = $senderGamePoints->points;

                    if (!$senderPoints or $senderPoints < $requiredPoints) {
                        session()->flash('fail_msg', 'You do not have enough points.');
                        return redirect()->back();
                    }

                    $senderGamePoints->points = $senderPoints - $requiredPoints;
                    $senderGamePoints->save();

//        fire update ranking job
                    RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

//                  seed data
                    $seed = new CompetitionPlayerUnlimtedFeePaid();
                    $seed->player_id = $userId;
                    $seed->competition_id = $competitionId;
                    $seed->save();

                }
            }
        });

//        start match
        $competitionMatch = new CompetitionMatch();
        $competitionMatch->competition_id = $matchRequest->competition_id;
        $competitionMatch->player_one = $matchRequest->player_id;
        $competitionMatch->player_two = Auth::id();
        $competitionMatch->save();
        $matchRequest->status = 'accepted';
        $matchRequest->save();
        session()->flash('success_received_msg' , 'Match Request Accepted and Match Created Successfully');
        return redirect()->back();
    }
    public function destory($id)
    {
        $matchRequest = CompetitionMatchRequest::findOrFail($id);
        if($matchRequest->delete())
        {
            session()->flash('success_msg', 'Match Request Deleted Successfully.');
        }
        else{
            session()->flash('fail_table', 'Deletion failed.');
        }
        return redirect()->back();
    }

    public function destorySentTeamMatchRequest($id)
    {
        $matchRequest = CompetitionTeamMatchRequest::findOrFail($id);
        if($matchRequest->delete())
        {
            session()->flash('success_team_msg', 'Match Request Deleted Successfully.');
        }
        else{
            session()->flash('fail_team_table', 'Deletion failed.');
        }
        return redirect()->back();
    }

    public function render()
    {
        $user = Auth::user();
        $sentMatchRequests = $user->sentMatchRequests;
//        check if player join any competition
        $receivedMatchRequests = CompetitionMatchRequest::where('status' , 'pending')->get();

//        for sent team match requests
        $captainTeams = $user->teams;

//        received team match requests
        $teamMatchRequests = CompetitionTeamMatchRequest::where('status' , 'pending')->get();

        return view('livewire.player.competition-match-requests' , compact('sentMatchRequests' , 'receivedMatchRequests'
                    , 'captainTeams' , 'teamMatchRequests'));
    }
}
