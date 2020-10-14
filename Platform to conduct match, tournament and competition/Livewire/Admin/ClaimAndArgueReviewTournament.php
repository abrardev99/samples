<?php

namespace App\Http\Livewire\Admin;

use App\ClaimArgueOneToOneMatchTournament;
use App\GamePoint;
use App\Jobs\RankingJob;
use App\TournamentScheduleMatch;
use Carbon\Carbon;
use Livewire\Component;

class ClaimAndArgueReviewTournament extends Component
{
    public function claimerWon($claimId){
//        set match status = completed, winner id, looser id and save
        $claim = ClaimArgueOneToOneMatchTournament::findOrFail($claimId);
        $winnerId = $claim->claimer_id;
        $match = $claim->match;
        $match->winner_id = $winnerId;
        $match->looser_id = $this->getOpponentId($match, $winnerId);
        $match->status = 'completed';
        $match->save();

//        set claim status to completed
        $claim->status = 'completed';
        $claim->save();

//        return back with success message
        session()->flash('success_msg_table' , 'Result Updated');
        return redirect()->back();
    }

    public function claimerLost($claimId){
        //        set match status = completed, winner id, looser id and save
        $claim = ClaimArgueOneToOneMatchTournament::findOrFail($claimId);
        $looserId = $claim->claimer_id;
        $match = $claim->match;
        $match->winner_id = $this->getOpponentId($match, $looserId);
        $match->looser_id = $looserId;
        $match->status = 'completed';
        $match->save();

//        set claim status to completed
        $claim->status = 'completed';
        $claim->save();

        //        deduct looser points(double because he is claiming won while he lost)
        $looserGamePoint = GamePoint::where('user_id' , $looserId)->where('game_id' , $match->game_id)->get()->first();
//        we deduct match points from both parties when player accept match request,
//        thats why, here we r not 2x from looser because we have points
        $looserGamePoint->points = $looserGamePoint->points - $match->points;
        $looserGamePoint->save();

        //        fire update ranking job
        RankingJob::dispatch($match->game_id)->delay(Carbon::now()->addSeconds(30));


//        return back with success message
        session()->flash('success_msg_table' , 'Result Updated');
        return redirect()->back();
    }

    public function getOpponentId($match, $authId){
        $looser_id = null;
        $player_one_id = $match->player_one;
        $player_two_id = $match->player_two;
        if($player_one_id != $authId)
            $looser_id = $player_one_id;
        else if($player_two_id != $authId)
            $looser_id = $player_two_id;

        return $looser_id;
    }
    public function render()
    {
        $tourMatches = TournamentScheduleMatch::all();
        return view('livewire.admin.claim-and-argue-review-tournament' , compact('tourMatches'));
    }
}
