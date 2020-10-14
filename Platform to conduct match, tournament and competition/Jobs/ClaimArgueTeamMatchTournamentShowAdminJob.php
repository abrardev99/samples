<?php

namespace App\Jobs;

use App\ClaimArgueTeamMatchTournament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClaimArgueTeamMatchTournamentShowAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $claimArgueMatchId;
    public $adminStatus;


    public function __construct($claimArgueMatchId, $adminStatus )
    {
        $this->claimArgueMatchId = $claimArgueMatchId;
        $this->adminStatus = $adminStatus;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $claimArgue = ClaimArgueTeamMatchTournament::find($this->claimArgueMatchId);
        if($claimArgue != null) {
            $claimArgue->is_show_to_admin = $this->adminStatus;
            $claimArgue->status = 'pending';
            $claimArgue->save();
        }
    }
}
