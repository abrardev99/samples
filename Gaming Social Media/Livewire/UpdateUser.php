<?php

namespace App\Http\Livewire;

use App\Traits\ViiManagerTrait;
use App\UserAvatar;
use App\UserProfileText;
use App\UserQA;
use App\UserVii;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateUser extends Component
{
    use WithFileUploads, ViiManagerTrait;

    public $userId;
    public $avatar;
    public $profileText;
    public $answers = [];
    public $questions = [] ;
    public $vii = null;

    public function mount($userId)
    {
        $this->userId = $userId;
        $userVii = UserVii::where('user_id' , $userId)->select('amount')->get()->first();
        if($userVii) {
            $this->vii = $userVii->amount;
        }
        $userQA = UserQA::where('user_id', $userId)->get();
        foreach ($userQA as $qa){
            $this->questions[] = $qa->question;
            $this->answers[] = $qa->answer;
        }

        array_unshift($this->questions, 0);
        unset($this->questions[0]);

        array_unshift($this->answers, 0);
        unset($this->answers[0]);

//        dd($this->questions);
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => ['image', 'max:1024'],
        ]);
    }

    public function store()
    {
        $this->validate([
            'answers.*' => ['required', Rule::notIn(0)],
        ]);

        $qas = UserQA::where('user_id', $this->userId);
        $qas->delete();

        foreach (UserQA::QUESTIONS as $key => $label) {
            UserQA::create([
                'user_id' => $this->userId,
                'question' => $key,
                'answer' => $this->answers[$key],
            ]);

        }

        if ($this->avatar) {
            UserAvatar::updateOrCreate(
                [
                    //Add unique field combo to match here
                    'user_id' => $this->userId,
                ],
                [
                    'avatar' => basename($this->avatar->store('public'))
                ]);
        }


        if ($this->profileText) {
            UserProfileText::updateOrCreate(
                [
                    //Add unique field combo to match here
                    'user_id' => $this->userId,
                ],
                [
                    'text' => $this->profileText,
                ]);
        }

        if ($this->vii) {
            $this->updateVii($this->userId, $this->vii);
        }

        return redirect()->route('admin.manage-users.edit', $this->userId);
    }

    public function render()
    {
        return view('livewire.update-user');
    }
}
