<?php

namespace App\Http\Livewire;

use App\SentenceCategory;
use App\ToolTip;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use \Chat;
use PharIo\Manifest\ElementCollection;

class ChatArena extends Component
{
    public $selectedConversationId = null;
    public $messageContent = null;
//    public $chat = null;

    public function updateMessageContent()
    {
        trim($this->messageContent);
    }

    public function updatedSelectedConversationId($id)
    {
        $this->selectedConversationId = $id;
//        $conversation = Chat::conversations()->getById($id);
//        $this->chat = collect(Chat::conversation($conversation)->setParticipant(Auth::user())->getMessages());
//        dd($this->chat);
    }

    public function sendMessage()
    {
        $conversation = Chat::conversations()->getById($this->selectedConversationId);
        Chat::message($this->messageContent)
            ->from(Auth::user())
            ->to($conversation)
            ->send();

        $this->messageContent = null;
    }
    public function render()
    {
        $userAssignedMessages = null;
        $chat = null;
        $conversations = Chat::conversations()->getById([1,2,3]);

        if($this->selectedConversationId !== null){
            $conversation = Chat::conversations()->getById($this->selectedConversationId);
            $chat = Chat::conversation($conversation)->setParticipant(Auth::user())->getMessages();
//            dd($chat);
        }

        if(Auth::user()->getRoleNames()->first() === 'user') {
            $userAssignedMessages = SentenceCategory::all();
//                Auth::user()->messages;
        }

        $tooltip = ToolTip::first();

        return view('livewire.chat-arena' , compact('conversations', 'userAssignedMessages' , 'chat', 'tooltip'));
    }
}
