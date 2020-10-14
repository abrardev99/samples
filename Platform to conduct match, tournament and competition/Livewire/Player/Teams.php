<?php

namespace App\Http\Livewire\Player;

use App\Models\Player\Team;
use App\Models\Player\TeamMembers;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Teams extends Component
{
    public function destroy($teamId){
        $team = Team::findOrFail($teamId);
        $team->delete();
        session()->flash('success_msg_table' , 'Team Deleted Successfully');
        return redirect()->back();
    }

    public function leaveTeam($teamId){
        $member = TeamMembers::where('team_id' , $teamId)->where('member_id' , Auth::id())->get()->first();
        $member->delete();
        session()->flash('success_msg_team_table' , 'You left Team Successfully');
        return redirect()->back();
    }

    public function render()
    {
        $receivedAndAcceptedRequests = TeamMembers::where('member_id', Auth::id())->where('status' , 'accepted')->get();
        $teamsAsCaptain = Auth::user()->teams;
        return view('livewire.player.teams' , compact('receivedAndAcceptedRequests' , 'teamsAsCaptain'));
    }
}
