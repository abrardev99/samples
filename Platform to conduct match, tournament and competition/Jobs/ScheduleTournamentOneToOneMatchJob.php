<?php

namespace App\Jobs;

use App\TournamentJoinedPlayer;
use App\TournamentScheduleMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleTournamentOneToOneMatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tournament_id = null;

    public function __construct($tournament_id)
    {
        $this->tournament_id = $tournament_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $players = TournamentJoinedPlayer::where('tournament_id' , $this->tournament_id)->get();
        if($players->count() > 1) {
            foreach ($players as $player) {
                foreach ($players as $innerPlayer){
                    $player_id = $player->player_id;
                    $innerPlayer_id = $innerPlayer->player_id;
                    if($player_id != $innerPlayer_id){
                        if(!TournamentScheduleMatch::where('player_one' , $player_id)->where('player_two' , $innerPlayer_id)->exists() and !TournamentScheduleMatch::where('player_one' , $innerPlayer_id)->where('player_two' , $player_id)->exists())
                        { $scheduleMatch = new TournamentScheduleMatch();
                            $scheduleMatch->player_one = $player_id;
                            $scheduleMatch->player_two = $innerPlayer_id;
                            $scheduleMatch->tournament_id = $this->tournament_id;
                            $scheduleMatch->round = '1';
                            $scheduleMatch->save();
                        }
                    }
                }
            }
        }
    }
}
