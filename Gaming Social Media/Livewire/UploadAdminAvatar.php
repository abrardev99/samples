<?php

namespace App\Http\Livewire;

use App\UserAvatar;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadAdminAvatar extends Component
{
    use WithFileUploads;

    public $userId;
    public $avatar;

    public function mount($userId)
    {
        $this->userId = $userId;
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => ['image', 'max:1024'],
        ]);
    }

    public function store()
    {
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

        return redirect()->route('profile');
    }
    public function render()
    {
        return view('livewire.upload-admin-avatar');
    }
}
