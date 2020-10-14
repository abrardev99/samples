<?php

namespace App\Http\Livewire\Player;

use App\GamePoint;
use App\Jobs\RankingJob;
use App\Jobs\TeamRankingJob;
use App\Models\Player\MatchRequests;
use App\Models\Player\OneToOneMatch;
use App\Models\Player\Team;
use App\TeamMatch;
use App\TeamMatchRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AllMatches extends Component
{
    public function destroy($id){
        $match = MatchRequests::findOrFail($id);
        if($match and $match->status != 'accepted'){
         if($match->delete())
            {
                 session()->flash('success_msg_table', 'Match Request Deleted Successfully.');
             }
            else{
            session()->flash('fail_msg_table', 'Deletion failed.');
            }
        }
        else{
            session()->flash('fail_msg_table', 'Match Request Not Found or Accepted or Accepted.');
        }
    }

//    received requests table operations
    public function accept($matchRequestId, $gameId){
        $matchRequest = MatchRequests::findOrFail($matchRequestId);
        if($matchRequest) {
            $requiredPoints = $matchRequest->points;

            $senderGamePoints = GamePoint::where('user_id', $matchRequest->user_id)->where('game_id', $gameId)->get()->first();
            $senderPoints = $senderGamePoints->points;

            if (!$senderPoints and $senderPoints < $requiredPoints) {
                session()->flash('fail_msg_table1', 'Sender does not have enough points.');
                return redirect()->back();
            }

            $acceptorGamePoints = GamePoint::where('user_id', $matchRequest->opponent_id)->where('game_id', $gameId)->get()->first();
            $acceptorPoints = $acceptorGamePoints->points;
            if ($acceptorPoints < $requiredPoints) {
                session()->flash('fail_msg_table1', 'Sender does not have enough points.');
                return redirect()->back();
            }
//        set status to accepted
            $matchRequest->status = 'accepted';
            $matchRequest->save();

//        deduct points from both accounts
            $senderGamePoints->points = $senderPoints - $requiredPoints;
            $senderGamePoints->save();
            $acceptorGamePoints->points = $acceptorPoints - $requiredPoints;
            $acceptorGamePoints->save();

//        create match
            $oneToOneMatch = new OneToOneMatch();
            $oneToOneMatch->game_id = $gameId;
            $oneToOneMatch->points = 2 * $requiredPoints;
            $oneToOneMatch->player_one_id = $matchRequest->user_id;
            $oneToOneMatch->player_two_id = $matchRequest->opponent_id;
            $oneToOneMatch->match_request_id = $matchRequest->id;
            $oneToOneMatch->save();
//        fire update ranking job
            RankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));
            session()->flash('success_msg_table', 'Match Started Successfully');
            return redirect('player/match/results');
        }
        else{
            session()->flash('fail_msg_table', 'Match Request Not Found');
            return redirect()->back();

        }

    }

    public function reject($matchRequestId){

        $matchRequest = MatchRequests::findOrFail($matchRequestId);
        if($matchRequest) {
        $matchRequest->status = 'rejected';
        $matchRequest->save();
        if($matchRequest){
            session()->flash('success_msg_table1', 'Successfully Rejected.');
        }
        else{
            session()->flash('fail_msg_table1', 'Status Updating Failed.');
        }
        }
        else{
            session()->flash('fail_msg_table', 'Match Request Not Found');
            return redirect()->back();

        }
    }

//    team methods
    public function destroySentTeamMatchReq($id){
        $match = TeamMatchRequest::findOrFail($id);
        if($match->delete())
        {
           session()->flash('success_msg_sent_table_team', 'Match Request Deleted Successfully.');
        }
        else{
           session()->flash('fail_msg_sent_table_team', 'Deletion failed.');
        }
    }

    public function acceptTeamMatchReq($teamMatchReqId, $gameId){
        $teamMatchReq = TeamMatchRequest::findOrFail($teamMatchReqId);
        $requiredPoints = $teamMatchReq->points;

//        check if match requester has points
        $requsterTeam = $teamMatchReq->team;
        if($requsterTeam->fee < $requiredPoints){
            session()->flash('fail_msg_received_table_team', 'Requester does not have enough points.');
            return redirect()->back();
        }

//        check if match request acceptor has points
        $acceptorTeam = $teamMatchReq->opponentTeam;
        if($acceptorTeam->fee < $requiredPoints){
            session()->flash('fail_msg_received_table_team', 'You don\'t have enough points.');
            return redirect()->back();
        }

//        deduct points from both accounts
        $requsterTeam->points = $requsterTeam->points - $requiredPoints;
        $requsterTeam->save();
        $acceptorTeam->points = $acceptorTeam->points - $requiredPoints;
        $requsterTeam->save();

//        create team match in team_matches table
        $teamMatch = new TeamMatch();
        $teamMatch->game_id = $gameId;
        $teamMatch->points = 2 * $requiredPoints;
        $teamMatch->team_one_id = $requsterTeam->id;
        $teamMatch->team_two_id = $acceptorTeam->id;
        $teamMatch->team_match_request_id = $teamMatchReqId;
        $teamMatch->save();

//        update request status to accepted if match created successfully
        $teamMatchReq->status = 'accepted';
        $teamMatchReq->save();

//        fire team ranking job
        TeamRankingJob::dispatch($gameId)->delay(Carbon::now()->addSeconds(30));

//        return to match results
        session()->flash('success_msg_team_table', 'Team Match Started Successfully');
        return redirect('player/match/results');
    }
    public function rejectTeamMatchReq($teamMatchReqId){
        $matchRequest = TeamMatchRequest::findOrFail($teamMatchReqId);
        $matchRequest->status = 'rejected';
        $matchRequest->save();
        if($matchRequest){
            session()->flash('success_msg_received_table_team', 'Successfully Rejected.');
        }
        else{
            session()->flash('fail_msg_received_table_team', 'Status Updating Failed.');
        }
    }

    public function render()
    {
        $matches = Auth::user()->matchRequests;
        $date = today()->format('Y-m-d');
        $receivedMatchRequests = MatchRequests::where('opponent_id' , Auth::id())->where('datetime', '>=', $date)->get();

//        team code
        $teamRequests = TeamMatchRequest::all();
        return view('livewire.player.all-matches' , compact('matches' , 'receivedMatchRequests' , 'teamRequests'));
    }
}
