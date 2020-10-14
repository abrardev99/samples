<?php

namespace App\Http\Livewire\Player;

use App\Competition;
use App\CompetitionLevel;
use App\CompetitionMatchRequest;
use App\CompetitionPlayerLevel;
use App\CompetitionPlayerMatchCounter;
use App\CompetitionPlayerUnlimtedFeePaid;
use App\CompetitionTeamDataSeeded;
use App\CompetitionTeamLevel;
use App\CompetitionTeamMatchCounter;
use App\CompetitionTeamMatchRequest;
use App\CompetitionTeamUnlimtedFeePaid;
use App\GamePoint;
use App\Jobs\RankingJob;
use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CompetitionCreateMatchRequest extends Component
{
    public $competitionId;
    public $competitionIdForTeam;
    public $teamId;
    public $captainTeams = [];

    public function updatedCompetitionId()
    {
        $this->validate([
            'competitionId' => 'required'
        ]);

        if ($this->competitionId == 0) {
            session()->flash('fail_msg', 'Please Select Competition');
            return redirect()->back();
        }

        $this->checkPlayerLevel();

    }

    public function checkPlayerLevel()
    {
        $playerLevel = CompetitionPlayerLevel::where('competition_id' , $this->competitionId)
            ->where('player_id' , Auth::id())->get()->first();

        if(!\App\CompetitionLevel::where('competition_id' , $this->competitionId)
            ->where('level' , $playerLevel->level)
            ->where('match_type' , 'player')->exists()){

            session()->flash('fail_msg', 'Not possible yet for your level. Try tomorrow.');
            return redirect()->back();

        }
    }

    public function updatedCompetitionIdForTeam()
    {
        $this->validate([
            'competitionIdForTeam' => 'required'
        ]);

        if ($this->competitionIdForTeam == 0) {
            session()->flash('fail_team_msg', 'Please Select Competition');
            return redirect()->back();
        }

        $competition = Competition::findOrFail($this->competitionIdForTeam);

        $this->captainTeams = Team::where('user_id' , Auth::id())->where('game_id' , $competition->game_id)->get();


    }

    public function updatedTeamId()
    {
        if ($this->competitionIdForTeam == 0) {
            session()->flash('fail_team_msg', 'Please Select Competition');
            return redirect()->back();
        }

        if ($this->teamId == 0) {
            session()->flash('fail_team_msg', 'Please Select Team');
            return redirect()->back();
        }

        $compTeamLevel = CompetitionTeamLevel::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->get()->first();
        if(!CompetitionLevel::where('competition_id', $this->competitionIdForTeam)
                             ->where('level', $compTeamLevel->level)
                             ->where('match_type' , 'team')->exists()){
            session()->flash('fail_team_msg', 'Not possible yet for your level. Try tomorrow.');
            return redirect()->back();
        }





    }

    public function store()
    {
        $this->validate([
            'competitionId' => 'required'
        ]);

        if ($this->competitionId == 0) {
            session()->flash('fail_msg', 'Please Select Competition');
            return redirect()->back();
        }

        $this->checkPlayerLevel();

        $user = Auth::user();
        $userId = $user->id;

        DB::transaction(function() use ($userId) {


            $competition = Competition::findOrFail($this->competitionId);
            $competitionFeeType = $competition->feeType;
            $gameId = $competition->game_id;

//        check fee type in competition
            if ($competitionFeeType == '10/match') {

//                check if initial data saved if not then save for once only

//            check counter because its 10/match
                $matchCounter = CompetitionPlayerMatchCounter::where('competition_id', $this->competitionId)->where('player_id', $userId)->get()->first();

                if ($matchCounter->counter == 0) {
                    //        check level, then charge fee from this level.
                    $compPlayerLevel = CompetitionPlayerLevel::where('competition_id', $this->competitionId)->where('player_id', $userId)->get()->first();
                    $compLevel = CompetitionLevel::where('competition_id', $this->competitionId)->where('level', $compPlayerLevel->level)
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
                if (!CompetitionPlayerUnlimtedFeePaid::where('competition_id', $this->competitionId)->where('player_id', $userId)->exists()) {
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
                    $seed->competition_id = $this->competitionId;
                    $seed->save();

                }
            }
        });

        $matchRequest = new CompetitionMatchRequest();
        $matchRequest->player_id = $userId;
        $matchRequest->competition_id = $this->competitionId;
        $matchRequest->save();

        session()->flash('success_msg', 'Competition Match Request Created Successfully');
        return redirect('player/competition/requests');

    }


    public function storeTeam()
    {
        $this->validate([
            'competitionIdForTeam' => 'required'
        ]);

        if ($this->competitionIdForTeam == 0) {
            session()->flash('fail_team_msg', 'Please Select Competition');
            return redirect()->back();
        }

        if ($this->teamId == 0) {
            session()->flash('fail_team_msg', 'Please Select Team');
            return redirect()->back();
        }


        DB::transaction(function () {

            $competition = Competition::findOrFail($this->competitionIdForTeam);
            $competitionFeeType = $competition->feeType;
            $gameId = $competition->game_id;
            $team = Team::findOrFail($this->teamId);
            $teamPoints = $team->points;

            if(!CompetitionTeamDataSeeded::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->exists()) {
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


//        check fee type in competition
            if ($competitionFeeType == '10/match') {

                $matchCounter = CompetitionTeamMatchCounter::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->get()->first();

                if ($matchCounter->counter == 0) {
                    //        check level, then charge fee from this level.
                    $compTeamLevel = CompetitionTeamLevel::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->get()->first();
                    $compLevel = CompetitionLevel::where('competition_id', $this->competitionIdForTeam)
                                                  ->where('level', $compTeamLevel->level)
                                                   ->where('match_type' , 'team')->get()->first();
                    $requiredPoints = $compLevel->fee;

                    if ($teamPoints < $requiredPoints) {
                        session()->flash('fail_team_msg', 'You do not have enough points.');
                        return redirect()->back();
                    }

                    $team->points = $teamPoints - $requiredPoints;
                    $team->save();

//        fire update ranking job
                    TeamRankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

                }

            } else if ($competitionFeeType == 'unlimited') {
                if (!CompetitionTeamUnlimtedFeePaid::where('competition_id', $this->competitionIdForTeam)->where('team_id', $this->teamId)->exists()) {
//                    charge from team points
                    $requiredPoints = $competition->fee;
                    if($teamPoints < $requiredPoints){
                        session()->flash('success_team_msg', 'Your team does not have enough points.');
                        return redirect()->back();
                    }

                    $team->points = $teamPoints - $requiredPoints;
                    $team->save();
                    TeamRankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

                    $seed = new CompetitionTeamUnlimtedFeePaid();
                    $seed->team_id = $this->teamId;
                    $seed->competition_id = $this->competitionIdForTeam;
                    $seed->save();

                }
            }

        });

        $matchRequest = new CompetitionTeamMatchRequest();
        $matchRequest->team_id = $this->teamId;
        $matchRequest->competition_id = $this->competitionIdForTeam;
        $matchRequest->save();

        session()->flash('success_team_msg', 'Competition Team Match Request Created Successfully');
        return redirect('player/competition/requests');

    }

    public function render()
    {
        $user = Auth::user();
        $joinedCompetitions = $user->joinedCompetition;

        return view('livewire.player.competition-create-match-request', compact('joinedCompetitions'));
    }
}
