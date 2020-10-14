<?php

namespace App\Jobs;

use App\GamePoint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RankingJob implements ShouldQueue
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
        $points = GamePoint::where('game_id', $this->game_id)->orderBy('points', 'desc') ->get();

        $i = 1;
        foreach ($points as $point){
            $point->ranking = $i;
            $i++;
            $point->save();
        }
        }

    }
}
