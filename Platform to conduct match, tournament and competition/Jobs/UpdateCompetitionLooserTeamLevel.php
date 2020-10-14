<?php

namespace App\Jobs;

use App\CompetitionLevel;
use App\CompetitionTeamLevel;
use App\CompetitionTeamPoints;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCompetitionLooserTeamLevel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $teamId = null;
    public $competitionId = null;


    public function __construct($teamId, $competitionId)
    {
        $this->teamId = $teamId;
        $this->competitionId = $competitionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sumOfTeamPoints = CompetitionTeamPoints::where('competition_id' , $this->competitionId)
            ->where('team_id' , $this->teamId)
            ->latest()
            ->take(10)
            ->sum('points');

        $teamLevel = CompetitionTeamLevel::where('competition_id' , $this->competitionId)
            ->where('team_id' , $this->teamId)
            ->get()
            ->first();

        $teamCurrentLevel = $teamLevel->level;

        $competitionLevel = CompetitionLevel::where('competition_id' , $this->competitionId)
            ->where('level' , $teamCurrentLevel)
            ->get()
            ->first();

        $competitionPromotionLevel = $competitionLevel->promotion_points;
        $competitionDegradeLevel = $competitionLevel->fallout_points;

        //        promote level
        if($sumOfTeamPoints >= $competitionPromotionLevel){
            $teamLevel->level = $teamCurrentLevel + 1;

        }else if($sumOfTeamPoints <= $competitionDegradeLevel){
//            degrade level
            if($teamLevel->level > 1){
                $teamLevel->level = $teamCurrentLevel - 1;
            }
        }else if($sumOfTeamPoints > $competitionDegradeLevel and $sumOfTeamPoints < $competitionPromotionLevel){
//            stay on same level
        }

        $teamLevel->save();

    }
}
