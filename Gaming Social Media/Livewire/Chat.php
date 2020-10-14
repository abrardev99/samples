<?php

namespace App\Http\Livewire;

use App\ChatMessage;
use App\Conversation;
use App\ToolTip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use phpDocumentor\Reflection\Types\Collection;

class Chat extends Component
{
    public $selectedConversationId = null;
    public $chat = null;
    public $message = null;
    public function mount($selectedConversationId = null){
        $this->selectedConversationId = $selectedConversationId;
        if($selectedConversationId)
        {
            $this->getChat();
        }
    }

    public function conversationSelect($conversationId, $authId, $senderId)
    {
        $this->selectedConversationId = $conversationId;
        $this->getChat();

        \App\Chat::where('sender_id' , $senderId)
            ->where('receiver_id' , $authId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);


    }

    public function updateMessage()
    {
        trim($this->message);
    }

    public function sendMessage()
    {
        $conversation = Conversation::findOrFail($this->selectedConversationId);
        $receiver_id = null;
        $userId = Auth::id();

        if($conversation->member_one == $userId)
            $receiver_id = $conversation->member_two;
        if($conversation->member_two == $userId)
            $receiver_id = $conversation->member_one;

        \App\Chat::create([
            'conversation_id' => $this->selectedConversationId,
            'sender_id' => $userId,
            'receiver_id' => $receiver_id,
            'message' => $this->message,
        ]);

        $this->message = null;

        $this->getChat();
    }

    public function getChat()
    {
        $this->chat = \App\Chat::where('conversation_id' , $this->selectedConversationId)->get();
    }
    public function render()
    {
        $userId = Auth::id();
        $conversations = Conversation::with(['memberOne' , 'memberTwo'])
            ->where('member_one' , $userId)
            ->orWhere('member_two' , $userId)
            ->orderByDesc('id')
            ->get();

        $tooltip = ToolTip::first();

        return view('livewire.chat' , compact('conversations', 'tooltip'));
    }
}
