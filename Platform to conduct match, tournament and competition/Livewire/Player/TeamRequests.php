<?php

namespace App\Http\Livewire\Player;

use App\Jobs\RankingJob;
use App\Jobs\TeamRankingJob;
use App\Models\Player\Team;
use App\Models\Player\TeamGamePoint;
use App\Models\Player\TeamMembers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeamRequests extends Component
{
//    captain sent teams methods
    public function destroy($id){
        $team = Team::findOrFail($id);
        if($team->delete())
        {
            session()->flash('success_msg_table', 'Team Deleted Successfully.');
        }
        else{
            session()->flash('fail_msg_table', 'Deletion failed.');
        }
    }

    public function accept($memberId, $gameId, $fee, $teamid){
        $team = Team::findOrFail($teamid);
        $points = \App\GamePoint::where('user_id' , Auth::id())->where('game_id' , $gameId)->get()->first();
        $point = $points->points;
        if($point < $fee) {
            session()->flash('fail_msg_table1', 'Not enough Points to accept');
            return redirect()->back();
        }
//            deduct points
            $points->points = $point - $fee;
            $points->save();
//            add points in team points
        $team->points = $team->points + $fee;
        $team->save();
        //            dispatch ranking job
        RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));
//            fire team ranking job
        TeamRankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));


        if($this->updateStatus($memberId, 'accepted'))
        {
            session()->flash('success_msg_table1', 'Successfully Accepted, You are now team member.');
            return redirect()->back();
        }
        else{
            session()->flash('fail_msg_table1', 'Status Updating Failed.');
            return redirect()->back();
        }
    }

    public function reject($memberId){
        if($this->updateStatus($memberId, 'rejected'))
        {
            session()->flash('success_msg_table1', 'Successfully Rejected.');
        }
        else{
            session()->flash('fail_msg_table1', 'Status Updating Failed.');
        }
    }

    public function updateStatus($memberId, $status) : bool {
        $member = TeamMembers::findOrFail($memberId);
        $member->status = $status;
        $member->save();
        if($member)
            return true;
        else
            return false;
    }

//    received team requests methods
    public function render()
    {
        $user = Auth::user();
        $userName = $user->name;
        $captainTeams = $user->teams;

        $receivedRequests = TeamMembers::where('member_id', $user->id)->get();
        return view('livewire.player.team-requests' , compact('userName' , 'captainTeams', 'receivedRequests'));
    }
}
