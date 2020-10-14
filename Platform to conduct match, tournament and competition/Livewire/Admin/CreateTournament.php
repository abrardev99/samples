<?php

namespace App\Http\Livewire\Admin;

use App\Jobs\RankingJob;
use App\Jobs\ScheduleTournamentOneToOneMatchJob;
use App\Jobs\ScheduleTournamentTeamMatchJob;
use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use App\TourInvitedContacts;
use App\Tournament;
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
    public $freeEntry = 'yes';
    public $inviteContacts = [];
    public $matchType = 1;

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
        $tournament->reward_points = 0;
        $tournament->withdraw_time = $this->withdrawTime;
        $tournament->max_players = $this->totalPlayers;
        $tournament->ranking_needed = $this->rankingNeeded;
        $tournament->first_round_time = Carbon::parse($this->firstRoundTime)->toTimeString();
        $tournament->final_round_time = Carbon::parse($this->finalRoundTime)->toTimeString();
        $tournament->tour_kind = $this->tourKind;
        $tournament->is_private_tour = 'no';
        $tournament->is_free_entry = $this->freeEntry;
        $user->tournaments()->save($tournament);
        $tournamentId = $tournament->id;

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

        }


        session()->flash('success_msg_table', 'Tournament created successfully. Matches will be schedule before given date time.');
        return redirect('admin/tournament/select');

    }

    public function storeTeam(){
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

//        create team tournament
        $tournament = new TournamentTeam();
        $tournament->game_id = $this->gameid;
//        $tournament->team_id = $this->teamId;
        $tournament->user_id = Auth::id(); // organizer id
        $tournament->name = $this->name;
        $tournament->type = $this->type;
        $datetime = Carbon::parse($this->datetime);
        $tournament->datetime = $datetime->toDateTimeString();
        //        fee points
        $tournament->points = $this->points;
//        reward points
        $tournament->reward_points = 0;
        $tournament->withdraw_time = $this->withdrawTime;
        $tournament->max_players = $this->totalPlayers;
        $tournament->ranking_needed = $this->rankingNeeded;
        $tournament->first_round_time = Carbon::parse($this->firstRoundTime)->toTimeString();
        $tournament->final_round_time = Carbon::parse($this->finalRoundTime)->toTimeString();
        $tournament->tour_kind = $this->tourKind;
        $tournament->save();
        $tournamentId = $tournament->id;

//       @todo  fire team match schedule job
        if($this->tourKind == 'pool') {
            ScheduleTournamentTeamMatchJob::dispatch($tournamentId)->delay($datetime);
        }
        if($this->tourKind == 'ko'){

        }

        session()->flash('success_msg_team_table', 'Team Tournament created successfully.');
        return redirect('admin/tournament/select');
    }

    public function render()
    {
        $games = \App\Models\Admin\Game::all();
        return view('livewire.admin.create-tournament' , compact('games'));
    }
}
