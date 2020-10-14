<?php

namespace App\Http\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Admin\Game as GameModel;

class Game extends Component
{
    public $games;
    public $gameId; // using for edit record only
    public $name;
    public $description;
    public $playing_time;
    public $type = 1;
    public $updateMode = false;

    public function mount(){
        $this->games = Auth::user()->games;
    }


    public function store()
    {
        $this->validate([
            'name' => 'required',
            'playing_time' => 'required|numeric',
        ]);

        $user = Auth::user();
        $game = new GameModel;
        $game->name = $this->name;
        $game->description = $this->description;
        $game->playing_time = $this->playing_time;
        $game->type = $this->type;
        $user->games()->save($game);
        $this->resetInput();
        $this->games = Auth::user()->games;
        session()->flash('success_msg', 'Game Saved Successfully.');
    }

    public function destroy($id){
        $game = GameModel::findOrFail($id);
        if($game->delete())
        {
            session()->flash('success_msg_table', 'Game Deleted Successfully.');
        }
        else{
            session()->flash('fail_msg_table', 'Deletion failed.');
        }
        $this->games = Auth::user()->games;
    }

    public function edit($id){
        $game = GameModel::findOrFail($id);
        $this->gameId = $game->id;
        $this->name = $game->name;
        $this->description = $game->description;
        $this->playing_time = $game->playing_time;
        $this->type = $game->type;
        $this->updateMode = true;
        session()->flash('success_msg', 'Please edit game below');

    }

    public function update(){

        $this->validate([
            'name' => 'required',
            'playing_time' => 'required|numeric',
        ]);


        $game = GameModel::findOrFail($this->gameId);

        $game->name = $this->name;
        $game->description = $this->description;
        $game->playing_time = $this->playing_time;
        $game->type = $this->type;
        $game->save();

        session()->flash('success_msg', 'Game updated successfully');

        $this->games = Auth::user()->games;

        $this->resetInput();
        $this->updateMode = false;
    }

    private function resetInput()
    {
        $this->name = null;
        $this->description = null;
        $this->playing_time = null;
        $this->gameId = null;
    }


    public function render()
    {
        return view('livewire.admin.game');
    }
}
