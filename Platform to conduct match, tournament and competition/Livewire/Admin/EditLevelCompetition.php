<?php

namespace App\Http\Livewire\Admin;

use App\Competition;
use App\CompetitionLevel;
use Livewire\Component;

class EditLevelCompetition extends Component
{
    public $competitionLevelId;
    public $competitionId;
    public $levels;
    public $level;
    public $matchType = 'player';
    public $promotionPoints;
    public $falloutPoints;
    public $fee;
    public $prizeType = 'fix';
    public $feeType = '';
    public $prize;

    public function mount($id){
        $this->competitionLevelId = $id;
    }

    public function updatedPromotionPoints()
    {
        $this->validate([
            'promotionPoints' => ['required', 'numeric', 'max:31', 'min:1']
        ]);
    }

    public function updatedFalloutPoints()
    {
        $this->validate([
            'falloutPoints' => ['required', 'numeric', 'max:30', 'min:-1']
        ]);
    }

    public function updatedCompetitionId()
    {
        if ($this->competitionId == 0) {
            session()->flash('fail_level_msg', 'Please Competition Name First');
            return redirect()->back();
        }
        $competition = Competition::findOrFail($this->competitionId);

        $this->levels = $competition->levels;
        $this->feeType = $competition->feeType;
    }

    public function updatedPrize()
    {
        if ($this->prizeType == 'percentage') {
            $this->validate([
                'prize' => ['required', 'numeric', 'max:100', 'min:1']
            ]);
        }

        if ($this->prizeType == 'fix') {
            $this->validate([
                'prize' => ['required', 'numeric']
            ]);
        }
    }

    public function update(){
        if ($this->competitionId == 0) {
            session()->flash('fail_level_msg', 'Please Competition Name First');
            return redirect()->back();
        }


        $this->validate([
            'promotionPoints' => ['required', 'numeric', 'max:31', 'min:1'],
            'falloutPoints' => ['required', 'numeric', 'max:30', 'min:-1'],
            'matchType' => ['required'],
            'level' => ['required'],
            'prizeType' => ['required'],
        ]);

        if ($this->prizeType == 'percentage') {
            $this->validate([
                'prize' => ['required', 'numeric', 'max:100', 'min:1']
            ]);
        }

        if ($this->prizeType == 'fix') {
            $this->validate([
                'prize' => ['required', 'numeric']
            ]);
        }

        if ($this->feeType == '10/match') {
            $this->validate([
                'fee' => ['required', 'numeric'],
            ]);
        }

        $level = CompetitionLevel::findOrFail($this->competitionLevelId);
        $level->competition_id = $this->competitionId;
        $level->level = $this->level;
        $level->match_type = $this->matchType;
        $level->promotion_points = $this->promotionPoints;
        $level->fallout_points = $this->falloutPoints;
        if($this->feeType == '10/match'){
            $level->fee = $this->fee;
        }
        $level->prize_type = $this->prizeType;
        $level->prize = $this->prize;
        $level->save();

        session()->flash('success_level_msg', 'Competition Level Updated Successfully');
        return redirect('admin/competition/display');
    }

    public function render()
    {
        $competitionLevel = CompetitionLevel::findOrFail($this->competitionLevelId);
        $this->competitionId = $competitionLevel->competition_id;
        $this->level = $competitionLevel->level;
        $this->matchType = $competitionLevel->match_type;
        $this->promotionPoints = $competitionLevel->promotion_points;
        $this->falloutPoints = $competitionLevel->fallout_points;
        $this->prizeType = $competitionLevel->prize_type;
        $this->feeType = $competitionLevel->fee_type;
        $this->prize = $competitionLevel->prize;
        $this->fee = $competitionLevel->fee;
        $this->levels = $competitionLevel->competition->levels;

        $competitions = Competition::all();

        return view('livewire.admin.edit-level-competition' , compact('competitions'));
    }
}
