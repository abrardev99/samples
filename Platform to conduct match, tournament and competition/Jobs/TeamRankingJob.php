<?php

namespace App\Jobs;

use App\Models\Player\Team;
use App\Models\Player\TeamGamePoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TeamRankingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $game_id = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($game_id)
    {
        $this->game_id = $game_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->game_id){
            $teams = Team::where('game_id', $this->game_id)->orderBy('fee', 'desc') ->get();

            $i = 1;
            foreach ($teams as $team){
                $team->ranking = $i;
                $i++;
                $team->save();
            }
        }
    }
}
