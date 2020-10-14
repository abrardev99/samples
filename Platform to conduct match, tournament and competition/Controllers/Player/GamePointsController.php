<?php

namespace App\Http\Controllers\Player;

use App\Competition;
use App\CompetitionMatch;
use App\CompetitionPlayerMatchCounter;
use App\Http\Controllers\Controller;
use App\Models\Player\TeamMembers;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamePointsController extends Controller
{
    public function index(){
        $user = Auth::user();
        $points = $user->points;
        $receivedAndAcceptedRequests = TeamMembers::where('member_id', Auth::id())->where('status' , 'accepted')->get();
        $teamsAsCaptain = Auth::user()->teams;
        $competitionPlayerLevels = $user->competitionPlayerLevel;

        return view('player.points' , compact('points' , 'receivedAndAcceptedRequests' , 'teamsAsCaptain'
        , 'competitionPlayerLevels'));
    }

    public function competitionInspect($competitionId)
    {
        $competition = Competition::findOrFail($competitionId);
        $userId = Auth::id();

        $playerMatches = CompetitionMatch::where('competition_id' , $competitionId)
                                           ->where('player_one' , $userId)->orWhere('player_two', $userId)
                                           ->where('status', 'completed')
                                           ->latest()->get();


        $winnerMatches = $playerMatches->where('winner_id', $userId);

        $looserMatches = $playerMatches->where('looser_id', $userId);



        return view('player.points-inspect' , compact('playerMatches' , 'competition',
                                                    'winnerMatches' , 'looserMatches'));
    }
}
