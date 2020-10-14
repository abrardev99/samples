<?php

namespace App\Http\Livewire\Admin;

use App\Tournament;
use App\TournamentTeam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SelectTournament extends Component
{
    public function render()
    {
        $userId = Auth::id();
        //        tournaments only which are before today's date
        $tournaments = Tournament::where('user_id' , $userId)->get();

        $teamTournaments = TournamentTeam::where('user_id' , $userId)->get();

        return view('livewire.admin.select-tournament' , compact('tournaments' , 'teamTournaments'));
    }
}
