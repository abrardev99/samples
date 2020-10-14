<?php

namespace App\Http\Livewire\Player;

use App\Jobs\RankingJob;
use App\Jobs\ScheduleTournamentOneToOneMatchJob;
use App\Jobs\ScheduleTournamentTeamMatchJob;
use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use App\TourInvitedContacts;
use App\Tournament;
use App\TournamentJoinedPlayer;
use App\TournamentJoinedTeam;
use App\TournamentTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateTournament extends Component
{
    public $gameid;
    public $name;
    public $type;
    public $datetime;
    public $points;
    public $withdrawTime;
    public $totalPlayers;
    public $rankingNeeded;
    public $firstRoundTime;
    public $finalRoundTime;
    public $tourKind;
    public $privateTourna = 'yes';
    public $freeEntry = 'yes';
    public $inviteContacts = [];
    public $matchType = 1;

    //    for team option
    public $teams = null;
    public $teamId;

    public function mount(){
        $this->tourKind = 0;
    }

    public function updatedGameId(){
        if($this->gameid == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        $userId = Auth::id();
        if($this->matchType == 2)
            $this->teams = Team::where('user_id' , $userId)->where('game_id' , $this->gameid)->get();
        }


    public function updatedPoints(){
        if($this->gameid == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }
        if($this->matchType == 1){
            $creatorPoints = \App\GamePoint::where('user_id' , Auth::id())->where('game_id' , $this->gameid)->get()->first();
            if(isset($creatorPoints) and $creatorPoints->points < $this->points){
                $this->points = null;
                session()->flash('fail_msg', 'You don\'t have enough points.');
                return redirect()->back();
            }
        }else if($this->matchType == 2){
            $team = Team::find($this->teamId);
            if(isset($this->teams) and $team->points < $this->points){
                $this->points = null;
                session()->flash('fail_msg', 'Your team have enough points.');
                return redirect()->back();
            }
        }
    }

    public function store(){
        if($this->gameid == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        $this->validate([
            'name' => 'required',
            'totalPlayers' => 'required',
            'firstRoundTime' => 'required',
            'withdrawTime' => 'required|max:24',
            'rankingNeeded' => 'required',
            'tourKind' => [
                'required',
                Rule::notIn([0]),
            ],
        ]);


        $creatorPoints = \App\GamePoint::where('user_id' , Auth::id())->where('game_id' , $this->gameid)->get()->first();
        if(isset($creatorPoints) and $creatorPoints->points < $this->points){
            session()->flash('fail_msg', 'You don\'t have enough points.');
            return redirect()->back();
        }

        $creatorPoints->points = $creatorPoints->points - $this->points;
        $creatorPoints->save();

        //        fire update ranking job
        RankingJob::dispatch($this->gameid)->delay(Carbon::now()->addSeconds(30));



        $user = Auth::user();
        $tournament = new Tournament();
        $tournament->game_id = $this->gameid;
        $tournament->name = $this->name;
        $tournament->type = $this->type;
        $datetime = Carbon::parse($this->datetime);
        $tournament->datetime = $datetime->toDateTimeString();
//        fee points
        $tournament->points = $this->points;
//        reward points
        $tournament->reward_points = $this->points;
        $tournament->withdraw_time = $this->withdrawTime;
        $tournament->max_players = $this->totalPlayers;
        $tournament->ranking_needed = $this->rankingNeeded;
        $tournament->first_round_time = Carbon::parse($this->firstRoundTime)->toTimeString();
        $tournament->final_round_time = Carbon::parse($this->finalRoundTime)->toTimeString();
        $tournament->tour_kind = $this->tourKind;
        $tournament->is_private_tour = $this->privateTourna;
        $tournament->is_free_entry = $this->freeEntry;
        $user->tournaments()->save($tournament);
        $tournamentId = $tournament->id;
//        add first creator of tournament as player
        $joinTournament = new TournamentJoinedPlayer();
        $joinTournament->player_id = $user->id;
        $joinTournament->tournament_id = $tournamentId;
        $joinTournament->save();

        if($tournament->is_free_entry == 'yes'){
            $this->validate([
                'inviteContacts' => 'required|array|max:5',
            ]);
            if(isset($this->inviteContacts)){
                foreach ($this->inviteContacts as $invited){
                    $tournamentInvitedContact = new TourInvitedContacts();
                    $tournamentInvitedContact->contact_id = $invited;
                    $tournamentInvitedContact->tournament_id = $tournamentId;
                    $tournamentInvitedContact->save();
                }
            }

        }

//        dispatch match scheduling worker
        if($this->tourKind == 'pool') {
            ScheduleTournamentOneToOneMatchJob::dispatch($tournamentId)->delay($datetime);
        }
        if($this->tourKind == 'ko'){
            ScheduleTournamentOneToOneMatchJob::dispatch($tournamentId)->delay($datetime);
        }


        session()->flash('success_msg_table', 'Tournament created successfully. Matches will be schedule before given date time.');
        return redirect('player/tournaments');

    }

    public function storeTeam(){
//        validations
        if($this->teamId == 0){
            session()->flash('fail_msg', 'Please Select Team First');
            return redirect()->back();
        }

        if($this->gameid == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        $this->validate([
            'name' => 'required',
            'totalPlayers' => 'required',
            'firstRoundTime' => 'required',
            'withdrawTime' => 'required|max:24',
            'rankingNeeded' => 'required',
            'tourKind' => [
                'required',
                Rule::notIn([0]),
            ],
        ]);

//        check if creator team has points
        $team = Team::find($this->teamId);
        if(isset($this->teams) and $team->points < $this->points){
            $this->points = null;
            session()->flash('fail_msg', 'Your team have enough points.');
            return redirect()->back();
        }

//        deduct creator points
        $team->points = $team->points - $this->points;
        $team->save();
        TeamRankingJob::dispatch($this->gameid)->delay(Carbon::now()->addSeconds(30));

//        create team tournament
        $tournament = new TournamentTeam();
        $tournament->game_id = $this->gameid;
        $tournament->team_id = $this->teamId;
        $tournament->user_id = Auth::id(); // organizer id
        $tournament->name = $this->name;
        $tournament->type = $this->type;
        $datetime = Carbon::parse($this->datetime);
        $tournament->datetime = $datetime->toDateTimeString();
   //        fee points
        $tournament->points = $this->points;
//        reward points
        $tournament->reward_points = $this->points;
        $tournament->withdraw_time = $this->withdrawTime;
        $tournament->max_players = $this->totalPlayers;
        $tournament->ranking_needed = $this->rankingNeeded;
        $tournament->first_round_time = Carbon::parse($this->firstRoundTime)->toTimeString();
        $tournament->final_round_time = Carbon::parse($this->finalRoundTime)->toTimeString();
        $tournament->tour_kind = $this->tourKind;
        $tournament->save();
        $tournamentId = $tournament->id;

//        add organizer team in joined teams table
        $joinedTeam = new TournamentJoinedTeam();
        $joinedTeam->team_id = $this->teamId;
        $joinedTeam->tournament_team_id = $tournamentId;
        $joinedTeam->save();

//        fire team match schedule worker
        if($this->tourKind == 'pool') {
            ScheduleTournamentTeamMatchJob::dispatch($tournamentId)->delay($datetime);
        }
        if($this->tourKind == 'ko'){
            ScheduleTournamentTeamMatchJob::dispatch($tournamentId)->delay($datetime);
        }

        session()->flash('success_msg_team_table', 'Tournament created successfully.');
        return redirect('player/tournaments');
    }

    public function render()
    {
        //        opponent can be player contact for now, have to discuss it with martin
        $favGames = \App\FavGames::where('game_id' , $this->gameid)->where('user_id' , '!=' , Auth::id())->get();

//        points
        $pointsGreaterOne = \App\GamePoint::where('user_id' , Auth::id())->where('points' , '>' , 1)->get();

        return view('livewire.player.create-tournament' , compact('favGames'  , 'pointsGreaterOne'));
    }
}
