<?php

namespace App\Http\Livewire;

use App\SentenceAssignedCategory;
use App\SentenceCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class SentenceManagement extends Component
{
    public $category;
    public $sentence;

    public function save()
    {
        $this->validate([
            'category' => ['required', Rule::notIn(0)],
            'sentence' => ['required', Rule::notIn(0)]
        ]);

        SentenceAssignedCategory::updateOrCreate([
            'user_id' => Auth::id(),
            'category_id' => $this->category,
            'sentence_id' => $this->sentence,
        ],
         [
            'user_id' => Auth::id(),
            'category_id' => $this->category,
            'sentence_id' => $this->sentence,
        ]);

        $this->sentence = 0;
        $this->category = 0;

    }

    public function render()
    {
        $userAssignedMessages = Auth::user()->messages;
        $sentenceCategory = SentenceCategory::all();
        return view('livewire.sentence-management', compact('sentenceCategory', 'userAssignedMessages'));
    }
}
