<?php

namespace App\Http\Livewire\Admin;

use App\Tournament;
use Livewire\Component;

class TournamentSchedule extends Component
{
    public $tournament;
    public function mount($id){
        $this->tournament = Tournament::findOrFail($id);
    }
    public function render()
    {
        return view('livewire.admin.tournament-schedule');
    }
}
