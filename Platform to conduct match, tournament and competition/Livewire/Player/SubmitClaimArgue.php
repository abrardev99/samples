<?php

namespace App\Http\Livewire\Player;

use App\Models\Player\OneToOneMatch;
use Livewire\Component;

class SubmitClaimArgue extends Component
{
    public $match;
    public $image;
    public $description;
    public function mount($id){

    }


    public function submit(){
        $this->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

    }
    public function render()
    {
        return view('livewire.player.submit-claim-argue');
    }
}
