<?php

namespace App\Jobs;

use App\CompetitionLevel;
use App\CompetitionMatchPlayerPoints;
use App\CompetitionPlayerLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCompetitionLooserLevel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId = null;
    public $competitionId = null;

    public function __construct($userId, $competitionId)
    {
        $this->userId = $userId;
        $this->competitionId = $competitionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sumOfPlayerPoints = CompetitionMatchPlayerPoints::where('competition_id' , $this->competitionId)
            ->where('player_id' , $this->userId)
            ->latest()
            ->take(10)
            ->sum('points');

        $playerLevel = CompetitionPlayerLevel::where('competition_id' , $this->competitionId)
            ->where('player_id' , $this->userId)
            ->get()
            ->first();

        $playerCurrentLevel = $playerLevel->level;

        $competitionLevel = CompetitionLevel::where('competition_id' , $this->competitionId)
            ->where('level' , $playerCurrentLevel)
            ->get()
            ->first();

        $competitionPromotionLevel = $competitionLevel->promotion_points;
        $competitionDegradeLevel = $competitionLevel->fallout_points;

//        promote level
        if($sumOfPlayerPoints >= $competitionPromotionLevel){
            $playerLevel->level = $playerCurrentLevel + 1;

        }else if($sumOfPlayerPoints <= $competitionDegradeLevel){
//            degrade level
            if($playerLevel->level > 1){
            $playerLevel->level = $playerCurrentLevel - 1;
            }
        }else if($sumOfPlayerPoints > $competitionDegradeLevel and $sumOfPlayerPoints < $competitionPromotionLevel){
//            stay on same level
        }

        $playerLevel->save();

    }
}
