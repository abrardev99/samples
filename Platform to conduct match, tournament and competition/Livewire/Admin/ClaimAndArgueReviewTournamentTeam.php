<?php

namespace App\Http\Livewire\Admin;

use App\ClaimArgueTeamMatchTournament;
use Livewire\Component;

class ClaimAndArgueReviewTournamentTeam extends Component
{
    public function claimerTeamWon($claimId){
        $claim = ClaimArgueTeamMatchTournament::findOrFail($claimId);
        $teamMatch = $claim->teamMatch;

        $claimerId = $claim->claimer_team_id;
        $teamMatch->winner_id = $claimerId;
        $teamMatch->looser_id = $this->getOpponentTeam($claimerId, $teamMatch);
        $teamMatch->status = 'completed';
        $teamMatch->save();

        $claim->status = 'completed';
        $claim->save();

        session()->flash('success_msg_team_table' , 'Results Updated');
        return redirect()->back();

    }

    public function claimerTeamLost($claimId){
        $claim = ClaimArgueTeamMatchTournament::findOrFail($claimId);
        $teamMatch = $claim->teamMatch;

        $claimerId = $claim->claimer_team_id;
        $teamMatch->winner_id = $this->getOpponentTeam($claimerId, $teamMatch);
        $teamMatch->looser_id = $claimerId;
        $teamMatch->status = 'completed';
        $teamMatch->save();

        $claim->status = 'completed';
        $claim->save();

        session()->flash('success_msg_team_table' , 'Results Updated');
        return redirect()->back();
    }

    public function getOpponentTeam($claimerId, $teamMatch) : int {
        $opponentTeamId = null;

        $teamOne = $teamMatch->teamOne;
        $teamTwo = $teamMatch->teamTwo;
        $teamOneId = $teamOne->id;
        $teamTwoId = $teamTwo->id;

        if($teamOneId != $claimerId)
            $opponentTeamId = $teamOneId;

        if($teamTwoId != $claimerId)
            $opponentTeamId = $teamTwoId;

        return $opponentTeamId;
    }
    public function render()
    {
        $tournamentTeamMatches = \App\TournamentScheduleTeamMatche::all();
        return view('livewire.admin.claim-and-argue-review-tournament-team' , compact('tournamentTeamMatches'));
    }
}
