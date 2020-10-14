<?php

namespace App\Jobs;

use App\TournamentJoinedTeam;
use App\TournamentScheduleTeamMatche;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScheduleTournamentTeamMatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tournamentMatchId = null;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tournamentMatchId)
    {
        $this->tournamentMatchId = $tournamentMatchId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $teams = TournamentJoinedTeam::where('tournament_team_id' , $this->tournamentMatchId)->get();
        if($teams->count() > 1){
            foreach ($teams as $player)
            {
                foreach ($teams as $innerPlayer){
                    $team_id = $player->team_id;
                    $innerTeam_id = $innerPlayer->team_id;
                    if($team_id != $innerTeam_id){
                        if(!TournamentScheduleTeamMatche::where('team_one_id' , $team_id)->where('team_two_id' , $innerTeam_id)->exists() and !TournamentScheduleTeamMatche::where('team_one_id' , $innerTeam_id)->where('team_two_id' , $team_id)->exists())
                        {
                            $scheduleMatch = new TournamentScheduleTeamMatche();
                            $scheduleMatch->team_one_id = $team_id;
                            $scheduleMatch->team_two_id = $innerTeam_id;
                            $scheduleMatch->tournament_team_id = $this->tournamentMatchId;
                            $scheduleMatch->round = '1';
                            $scheduleMatch->save();
                        }
                    }
                }
            }
        }
    }
}
