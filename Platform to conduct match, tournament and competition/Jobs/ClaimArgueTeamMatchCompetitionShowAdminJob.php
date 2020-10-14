<?php

namespace App\Jobs;

use App\ClaimArgueCompetitionTeamMatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClaimArgueTeamMatchCompetitionShowAdminJob implements ShouldQueue
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
        $claimArgue = ClaimArgueCompetitionTeamMatch::find($this->claimArgueMatchId);
        if($claimArgue != null) {
            $claimArgue->is_show_to_admin = $this->adminStatus;
            $claimArgue->status = 'pending';
            $claimArgue->save();
        }
    }
}
