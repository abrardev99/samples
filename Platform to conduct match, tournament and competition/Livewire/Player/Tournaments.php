<?php

namespace App\Http\Livewire\Player;

use App\GamePoint;
use App\Jobs\RankingJob;
use App\Tournament;
use App\TournamentJoinedPlayer;
use App\TournamentJoinedTeam;
use App\TournamentTeam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Tournaments extends Component
{
    public function join($tournamentId, $gameId){
        $tournament = Tournament::findOrFail($tournamentId);
        $userId = Auth::id();
        $points = GamePoint::where('user_id' , $userId)->where('game_id' , $gameId)->get()->first();
        if(isset($points) and $tournament->ranking_needed >= $points->ranking  ){
//            deduct player points
            $points->points = $points->points - $tournament->points;
            $points->save();

//            add points in tournament points
            $tournament->reward_points = $tournament->reward_points + $tournament->points;
            $tournament->save();

//            dispatch ranking job
            RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(90));

//            save player in joined players table
            $joinTournament = new TournamentJoinedPlayer();
            $joinTournament->player_id = $userId;
            $joinTournament->tournament_id = $tournamentId;
            $joinTournament->save();
            session()->flash('success_msg_table', 'You successfully joined the tournament');
            return redirect()->back();
        }else{
            session()->flash('fail_msg_table', 'You ranking is lower than required');
            return redirect()->back();
        }
    }

    public function joinFree($tournamentId, $gameId){
        $tournament = Tournament::findOrFail($tournamentId);
        $userId = Auth::id();
        $points = GamePoint::where('user_id' , $userId)->where('game_id' , $gameId)->get()->first();
        if($tournament->ranking_needed >= $points->ranking  ){
//           no need to deduct player points

//          no need to add points in tournament points

//            save player in joined players table
            $joinTournament = new TournamentJoinedPlayer();
            $joinTournament->player_id = $userId;
            $joinTournament->tournament_id = $tournamentId;
            $joinTournament->save();
            session()->flash('success_msg_table', 'You successfully joined the tournament(Free)');
            return redirect()->back();
        }else{
            session()->flash('fail_msg_table', 'You ranking is lower than required');
            return redirect()->back();
        }
    }

    public function withdraw($tournamentId){
//        check if player withdrawing from tournament before set time
//        player will not ever get points back
        $tournament = Tournament::findOrFail($tournamentId);
        $tournamentDateTime = $tournament->datetime;
        $diffInHours = Carbon::parse($tournamentDateTime)->diffInHours();
        if($diffInHours > $tournament->withdraw_time){
            // have time to withdraw
            $userId = Auth::id();
//            delete player in joined players table
            $joinTournament = TournamentJoinedPlayer::where('player_id',$userId)->where('tournament_id' , $tournamentId)->delete();
            session()->flash('success_msg_table', 'You successfully withdrawn from the tournament');
            return redirect()->back();
        }else{
            session()->flash('fail_msg_table', 'Unable to Withdraw');
            return redirect()->back();
        }


    }

    public function withdrawTeam($tournamentId, $jonedTeamId){
        $tournament = Tournament::findOrFail($tournamentId);
        $tournamentDateTime = $tournament->datetime;
        $diffInHours = Carbon::parse($tournamentDateTime)->diffInHours();
        if($diffInHours > $tournament->withdraw_time){
            $joinedTeam = TournamentJoinedTeam::findOrFail($jonedTeamId);
            $joinedTeam->delete();
            session()->flash('success_msg_table', 'You successfully withdrawn from the tournament');
            return redirect()->back();
        }else{
            session()->flash('fail_msg_table', 'Unable to Withdraw');
            return redirect()->back();
        }
    }

    public function render()
    {
//        tournaments only which are before today's date
        $tournaments = Tournament::where('datetime', '>', DB::raw('NOW()'))->get();

        $teamTournaments = TournamentTeam::where('datetime', '>', DB::raw('NOW()'))->get();
        return view('livewire.player.tournaments' , compact('tournaments' , 'teamTournaments'));
    }
}
