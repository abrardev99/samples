<?php

namespace App\Http\Livewire\Player;

use App\Tournament;
use App\TournamentJoinedPlayer;
use App\TournamentJoinedTeam;
use App\TournamentTeam;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TournamentsDisplay extends Component
{
    public function render()
    {
        $userId = Auth::id();
        //        tournaments only which are before today's date
        $tournaments = Tournament::where('user_id' , $userId)->get();

        $teamTournaments = TournamentTeam::where('user_id' , $userId)->get();

        $tournamentsYouJoined = TournamentJoinedPlayer::where('player_id' , $userId)->get();

        $teamTournamentsYourTeamJoined = TournamentJoinedTeam::all();
        return view('livewire.player.tournaments-display' , compact('tournaments' , 'teamTournaments' , 'tournamentsYouJoined' , 'teamTournamentsYourTeamJoined'));
    }
}
