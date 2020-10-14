<?php

namespace App\Http\Livewire\Player;

use App\Competition;
use App\CompetitionLevel;
use App\CompetitionTeamDataSeeded;
use App\CompetitionTeamLevel;
use App\CompetitionTeamMatch;
use App\CompetitionTeamMatchCounter;
use App\CompetitionTeamMatchRequest;
use App\CompetitionTeamUnlimtedFeePaid;
use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CompetitionAcceptTeamRequest extends Component
{
    public $requestId;
    public $competitionIdForTeam;
    public $gameId;
    public $teamId;
    public function mount($requestId, $competitionId, $gameId)
    {
        $this->requestId = $requestId;
        $this->competitionIdForTeam = $competitionId;
        $this->gameId = $gameId;

    }

    public function accept()
    {
        $this->validate([
            'teamId' => 'required'
        ]);

        if($this->teamId == 0){
            session()->flash('fail_msg'  , 'Team name is required');
            return redirect()->back();
        }





        if (!CompetitionTeamDataSeeded::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->exists()) {
            //        assign level 1
            $playerLevel = new CompetitionTeamMatchCounter();
            $playerLevel->team_id = $this->teamId;
            $playerLevel->competition_id = $this->competitionIdForTeam;
            $playerLevel->counter = 0;
            $playerLevel->save();

//        make competition counter
            $counter = new CompetitionTeamLevel();
            $counter->team_id = $this->teamId;
            $counter->competition_id = $this->competitionIdForTeam;
            $counter->level = 1;
            $counter->save();

//                confirm that data seeded
            $seed = new CompetitionTeamDataSeeded();
            $seed->team_id = $this->teamId;
            $seed->competition_id = $this->competitionIdForTeam;
            $seed->save();
        }


//            check if teams level equal then proceed
        $matchRequest = CompetitionTeamMatchRequest::findOrFail($this->requestId);
        $senderTeam = CompetitionTeamLevel::where('competition_id', $this->competitionIdForTeam)->where('team_id', $matchRequest->team_id)->get()->first();
        $acceptorTeam = CompetitionTeamLevel::where('team_id' , $this->teamId)->where('competition_id' , $this->competitionIdForTeam)->get()->first();

        if($senderTeam->level != $acceptorTeam->level){
            session()->flash('fail_msg'  , 'Teams Level Mismatch');
            return redirect()->back();
        }

        //        check if team data seeded
            $competition = Competition::findOrFail($this->competitionIdForTeam);
            $competitionFeeType = $competition->fee_type;


            //        check fee type in competition
            if ($competitionFeeType == '10/match') {

                $matchCounter = CompetitionTeamMatchCounter::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->get()->first();

                if ($matchCounter->counter == 0) {
                    //        check level, then charge fee from this level.
                    $compLevel = CompetitionLevel::where('competition_id', $this->competitionIdForTeam)->where('level', $acceptorTeam->level)->get()->first();

                    $requiredPoints = $compLevel->fee;

                    if ($acceptorTeam->points < $requiredPoints) {
                        session()->flash('fail_team_msg', 'You do not have enough points.');
                        return redirect()->back();
                    }

                    $acceptorTeam->points = $acceptorTeam->points - $requiredPoints;
                    $acceptorTeam->save();

//        fire update ranking job
                    TeamRankingJob::dispatch($this->gameId)->delay(Carbon::now()->addSeconds(30));

                }

            } else if ($competitionFeeType == 'unlimited') {
                if (!CompetitionTeamUnlimtedFeePaid::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->exists()) {
//                    charge from team points
                    $requiredPoints = $competition->fee;
                    if ($acceptorTeam->level < $requiredPoints) {
                        session()->flash('fail_msg', 'Your team does not have enough points.');
                        return redirect()->back();
                    }

                    $acceptorTeam->points = $acceptorTeam->points - $requiredPoints;
                    $acceptorTeam->save();
                    TeamRankingJob::dispatch($this->gameId)->delay(Carbon::now()->addSeconds(30));

                    $seed = new CompetitionTeamUnlimtedFeePaid();
                    $seed->team_id = $this->teamId;
                    $seed->competition_id = $this->competitionIdForTeam;
                    $seed->save();

                }
            }

       $matchRequest->status = 'accepted';
       $matchRequest->save();

//            create team match
        $teamMatch = new CompetitionTeamMatch();
        $teamMatch->team_one = $matchRequest->team_id; // sender
        $teamMatch->team_two = $this->teamId; // acceptor
        $teamMatch->competition_id = $this->competitionIdForTeam;
        $teamMatch->save();

        session()->flash('success_msg_team_table' , 'Team Match Created Successfully');
        return redirect('player/competition/matches');
    }

    public function render()
    {
        $teams = Team::where('user_id' , Auth::id())->where('game_id' , $this->gameId)->get();
        return view('livewire.player.competition-accept-team-request' , compact('teams'));
    }
}
