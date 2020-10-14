<?php

namespace App\Http\Controllers\player;

use App\ClaimArgueCompetitionTeamMatch;
use App\ClaimArgueOneToOneCompetitionMatch;
use App\ClaimArgueOneToOneMatch;
use App\ClaimArgueOneToOneMatchTournament;
use App\ClaimArgueTeamMatch;
use App\ClaimArgueTeamMatchTournament;
use App\CompetitionMatch;
use App\CompetitionTeamMatch;
use App\Http\Controllers\Controller;
use App\Jobs\ClaimArgueOneToOneMatchCompetitionShowAdminJob;
use App\Jobs\ClaimArgueOneToOneMatchShowAdminJob;
use App\Jobs\ClaimArgueOneToOneMatchTournamentShowAdminJob;
use App\Jobs\ClaimArgueTeamMatchCompetitionShowAdminJob;
use App\Jobs\ClaimArgueTeamMatchShowAdminJob;
use App\Jobs\ClaimArgueTeamMatchTournamentShowAdminJob;
use App\Models\Player\OneToOneMatch;
use App\TeamMatch;
use App\TournamentScheduleMatch;
use App\TournamentScheduleTeamMatche;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmitClaimController extends Controller
{
    public function index($id){
        $match = OneToOneMatch::findOrFail($id);
        return view('player.submit-claim' , compact('match'));
    }

    public function store(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'match_id' => 'required',
        ]);

//        upload image
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $submitClaim = new ClaimArgueOneToOneMatch();
        $submitClaim->one_to_one_matches_id = $request->match_id;
        $submitClaim->claimer_id = Auth::id();
        $submitClaim->image = $imageName;
        $submitClaim->description = $request->description;
        $submitClaim->claim = 'i won';
        $submitClaim->save();

//        fire job that will execute after 5 min and will set show to admin to true.
        ClaimArgueOneToOneMatchShowAdminJob::dispatch($submitClaim->id, true)->delay(Carbon::now()->addMinutes(5));

        session()->flash('success_msg_table' , 'Claim Submitted Successfully, But you still have Five minutes to change decision to avoid penalty');
        return redirect('player/ca');
    }

//    team
    public function indexTeam($id){
        $teamMatch = TeamMatch::findOrFail($id);
        return view('player.submit-claim-team' , compact('teamMatch'));
    }

    public function storeTeam(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'match_id' => 'required',
        ]);

        $matchId = $request->match_id;
        $teamMatch = TeamMatch::findOrFail($matchId);
        $teamOne = $teamMatch->teamOne;
        $teamTwo = $teamMatch->teamTwo;

//        know user and opponent team
        $authUserTeamId = null;
        $userId = Auth::id();
        if($teamOne->user_id == $userId){
            $authUserTeamId = $teamOne->id;}
        elseif ($teamTwo->user_id == $userId)
        {
            $authUserTeamId = $teamTwo->id;
        }

        //        upload image
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $submitClaim = new ClaimArgueTeamMatch();
        $submitClaim->team_matche_id = $matchId;
        $submitClaim->claimer_team_id = $authUserTeamId;
        $submitClaim->image = $imageName;
        $submitClaim->description = $request->description;
        $submitClaim->claim = 'we won';
        $submitClaim->save();

        //        fire job that will execute after 5 min and will set show to admin to true.
        ClaimArgueTeamMatchShowAdminJob::dispatch($submitClaim->id, true)->delay(Carbon::now()->addMinutes(5));

        session()->flash('success_msg_table' , 'Claim Submitted Successfully, But you still have Five minutes to change decision to avoid penalty');
        return redirect('player/team/ca');
    }

    public function indexTournament($id){
        $match = TournamentScheduleMatch::findOrFail($id);
        return view('player.submit-claim-tournament' , compact('match'));
    }

    public function storeTournament(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'match_id' => 'required',
        ]);

//        upload image
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $matchId = $request->match_id;
        $match = TournamentScheduleMatch::find($matchId);
        $tournament = $match->tournament;

        $submitClaim = new ClaimArgueOneToOneMatchTournament();
        $submitClaim->tournament_match_id = $matchId;
        $submitClaim->claimer_id = Auth::id();
        $submitClaim->organizer_id = $tournament->user_id;
        $submitClaim->image = $imageName;
        $submitClaim->description = $request->description;
        $submitClaim->claim = 'i won';
        $submitClaim->save();

        //        fire job that will execute after 5 min and will set show to admin to true.
        ClaimArgueOneToOneMatchTournamentShowAdminJob::dispatch($submitClaim->id, true)->delay(Carbon::now()->addMinutes(5));

        session()->flash('success_msg_table' , 'Claim Submitted Successfully, But you still have Five minutes to change decision to avoid penalty');
        return redirect('player/ca');

    }

    public function indexTournamentTeam($id){
        $teamMatch = TournamentScheduleTeamMatche::findOrFail($id);
        return view('player.submit-claim-tournament-team' , compact('teamMatch'));
    }

    public function storeTournamentTeam(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'match_id' => 'required',
        ]);

        $matchId = $request->match_id;
        $teamMatch = TournamentScheduleTeamMatche::findOrFail($matchId);
        $tournament = $teamMatch->teamTournament;
        $teamOne = $teamMatch->teamOne;
        $teamTwo = $teamMatch->teamTwo;

        //        know user and opponent team
        $authUserTeamId = null;
        $userId = Auth::id();
        if($teamOne->user_id == $userId){
            $authUserTeamId = $teamOne->id;}
        elseif ($teamTwo->user_id == $userId)
        {
            $authUserTeamId = $teamTwo->id;
        }

        //        upload image
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $submitClaim = new ClaimArgueTeamMatchTournament();
        $submitClaim->tournament_team_match_id = $matchId;
        $submitClaim->claimer_team_id = $authUserTeamId;
        $submitClaim->organizer_id = $tournament->user_id;
        $submitClaim->image = $imageName;
        $submitClaim->description = $request->description;
        $submitClaim->claim = 'we won';
        $submitClaim->save();

        //        fire job that will execute after 5 min and will set show to admin to true.
        ClaimArgueTeamMatchTournamentShowAdminJob::dispatch($submitClaim->id, true)->delay(Carbon::now()->addMinutes(5));

        session()->flash('success_msg_table' , 'Claim Submitted Successfully, But you still have Five minutes to change decision to avoid penalty');
        return redirect('player/team/ca');

    }

    public function indexCompetition($id)
    {
        $match = CompetitionMatch::findOrFail($id);
        return view('player.submit-claim-competition' , compact('match'));
    }

    public function storeCompetition(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'match_id' => 'required',
        ]);

        //        upload image
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $submitClaim = new ClaimArgueOneToOneCompetitionMatch();
        $submitClaim->competition_match_id = $request->match_id;
        $submitClaim->claimer_id = Auth::id();
        $submitClaim->image = $imageName;
        $submitClaim->description = $request->description;
        $submitClaim->claim = 'i won';
        $submitClaim->save();

        //        fire job that will execute after 5 min and will set show to admin to true.
        ClaimArgueOneToOneMatchCompetitionShowAdminJob::dispatch($submitClaim->id, true)->delay(Carbon::now()->addMinutes(5));

        session()->flash('success_msg_table' , 'Claim Submitted Successfully, But you still have Five minutes to change decision to avoid penalty');
        return redirect('player/ca');
    }


    public function indexCompetitionTeam($id)
    {
        $teamMatch = CompetitionTeamMatch::findOrFail($id);
        return view('player.submit-claim-competition-team' , compact('teamMatch'));
    }

    public function storeCompetitionTeam(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'match_id' => 'required',
        ]);

        $matchId = $request->match_id;
        $teamMatch = CompetitionTeamMatch::findOrFail($matchId);
        $teamOne = $teamMatch->teamOne;
        $teamTwo = $teamMatch->teamTwo;

        //        know user and opponent team
        $authUserTeamId = null;
        $userId = Auth::id();
        if($teamOne->user_id == $userId){
            $authUserTeamId = $teamOne->id;}
        elseif ($teamTwo->user_id == $userId)
        {
            $authUserTeamId = $teamTwo->id;
        }

        //        upload image
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $submitClaim = new ClaimArgueCompetitionTeamMatch();
        $submitClaim->competition_team_match_id = $request->match_id;
        $submitClaim->claimer_team_id = $authUserTeamId;
        $submitClaim->image = $imageName;
        $submitClaim->description = $request->description;
        $submitClaim->claim = 'i won';
        $submitClaim->save();


        //        fire job that will execute after 5 min and will set show to admin to true.
        ClaimArgueTeamMatchCompetitionShowAdminJob::dispatch($submitClaim->id, true)->delay(Carbon::now()->addMinutes(5));

        session()->flash('success_msg_table' , 'Claim Submitted Successfully, But you still have Five minutes to change decision to avoid penalty');
        return redirect('player/team/ca');


    }

}
