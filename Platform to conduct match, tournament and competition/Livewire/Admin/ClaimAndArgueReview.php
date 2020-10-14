<?php

namespace App\Http\Livewire\Admin;

use App\ClaimArgueOneToOneMatch;
use App\GamePoint;
use App\Jobs\RankingJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClaimAndArgueReview extends Component
{
    public function claimerWon($argueId){
        $claim = ClaimArgueOneToOneMatch::findOrFail($argueId);
        $match = $claim->match;
        $winnerId = $claim->claimer_id;
        $looserId = $this->getOpponentId($match, $winnerId);

        $gamePoint = GamePoint::where('user_id' , $winnerId)->where('game_id' , $match->game_id)->get()->first();
        $gamePoint->points = $gamePoint->points + $match->points;
        $gamePoint->save();

        $match->status = 'completed';
        $match->winner_id = $winnerId;
        $match->looser_id = $looserId;
        $match->save();

        $claim->status = 'completed';
        $claim->save();

//        fire update ranking job
        RankingJob::dispatch($match->game_id);

        session()->flash('success_msg_table', 'Claimer Won the Match');
        return redirect()->back();
    }
    public function getOpponentId($match, $authId){
        $looser_id = null;
        $player_one_id = $match->player_one_id;
        $player_two_id = $match->player_two_id;
        if($player_one_id != $authId)
            $looser_id = $player_one_id;
        else if($player_two_id != $authId)
            $looser_id = $player_two_id;

        return $looser_id;
    }
    public function claimerLost($argueId){
        $claim = ClaimArgueOneToOneMatch::findOrFail($argueId);
        $match = $claim->match;
        $looserId = $claim->claimer_id;
        $winnerId = $this->getOpponentId($match, $looserId);

//        deduct looser points(double because he is claiming won while he lost)
        $looserGamePoint = GamePoint::where('user_id' , $winnerId)->where('game_id' , $match->game_id)->get()->first();
//        we deduct match points from both parties when player accept match request,
//        thats why, here we r not 2x from looser because we have points
        $looserGamePoint->points = $looserGamePoint->points - $match->points;
        $looserGamePoint->save();

//        update winner points
        $winnerGamePoint = GamePoint::where('user_id' , $winnerId)->where('game_id' , $match->game_id)->get()->first();
        $winnerGamePoint->points = $winnerGamePoint->points + (int)$match->points * 2;
        $winnerGamePoint->save();

        $match->status = 'completed';
        $match->winner_id = $winnerId;
        $match->looser_id = $looserId;
        $match->save();

        $claim->status = 'completed';
        $claim->save();

//        fire update ranking job
        RankingJob::dispatch($match->game_id)->delay(Carbon::now()->addSeconds(90));

        session()->flash('success_msg_table', 'Claimer Lost the Match');
        return redirect()->back();
    }
    public function render()
    {
        $matches = \App\Models\Player\OneToOneMatch::all();
        return view('livewire.admin.claim-and-argue-review' , compact('matches'));
    }
}
