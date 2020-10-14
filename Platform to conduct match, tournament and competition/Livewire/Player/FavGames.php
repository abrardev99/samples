<?php

namespace App\Http\Livewire\Player;

use App\Models\Admin\Game;
use App\GamePoint;
use App\Jobs\RankingJob;
use App\PlayerSelectGameEver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FavGames extends Component
{
    public $selectedGames;
    public $newgame = 0;
    public $nickname;
    public $updateMode = false;


    public function store(){

        if($this->newgame == 0){
            session()->flash('fail_msg', 'Please Select Game First');
            return redirect()->back();
        }

        $this->validate([
            'nickname' => 'required',
        ]);

        $userId = Auth::user()->id;
        $user = Auth::user();

        $favGame = new \App\FavGames();
        $favGame->user_id = $userId;
        $favGame->game_id = $this->newgame;
        $favGame->nickname = $this->nickname;
        $favGame->save();

        if(!PlayerSelectGameEver::select('id')->where('user_id',$userId)->where('game_id' , $this->newgame)->exists()){
            // Save that player select gave ever and award points
            PlayerSelectGameEver::create(['user_id' => $userId, 'game_id' => $this->newgame]);
            $gamePoints = new GamePoint();
            $gamePoints->user_id = $userId;
            $gamePoints->fav_game_id = $favGame->id;
            $gamePoints->game_id = $favGame->game_id;
            $gamePoints->points = 100;
            $gamePoints->ranking = null;
            $gamePoints->save();

//            dispatch ranking job
            RankingJob::dispatch($favGame->game_id);

        }

        $this->resetInput();
        session()->flash('success_msg', 'Game Selected Successfully.');


    }


    public function destroy($id){

        $game = \App\FavGames::findOrFail($id);
        if($game->delete())
        {
            session()->flash('success_msg_table', 'Game Deleted Successfully.');
        }
        else{
            session()->flash('fail_msg_table', 'Deletion failed.');
        }
        $this->games = Auth::user()->selectedGames;
    }


    private function resetInput()
    {
        $this->newgame = 0;
        $this->nickname = null;
    }

    public function render()
    {
        $this->selectedGames = Auth::user()->selectedGames;
        $games = Game::all();

        return view('livewire.player.fav-games', compact('games'));
    }
}
