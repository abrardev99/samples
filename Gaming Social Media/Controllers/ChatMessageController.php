<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChatMessageController extends Controller
{
    public function index()
    {
        $updateMode = false;
        $users = User::role('user')->get();
        $messages = ChatMessage::with('users')->get();
        return view('admin.message.index', compact('users' , 'messages' , 'updateMode'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $request->validate([
            'message' => ['required'],
            'users' => ['required' , Rule::notIn(0)],
        ]);

        $message = ChatMessage::create([
            'message' => $request->message,
            'user_id' => Auth::id(),
        ]);

        $message->users()->attach($request->users);

        toast('Message created successfully' , 'success');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ChatMessage  $chatMessage
     * @return \Illuminate\Http\Response
     */
    public function show(ChatMessage $chatMessage)
    {
        //
    }

    public function edit($chatMessageId)
    {
        $updateMode = true;
        $chatMessage = ChatMessage::findOrFail($chatMessageId);
//        $chatMessageUsers =  $chatMessage->users;
        $users = User::role('user')->get();
        $messages = ChatMessage::with('users')->get();

        toast('Update Message here' , 'info');
        return view('admin.message.index', compact('users' , 'messages' , 'updateMode', 'chatMessage'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ChatMessage  $chatMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ChatMessage $chatMessage)
    {
        //
    }


    public function destroy($chatMessageId)
    {
        $chatMessage = ChatMessage::findOrFail($chatMessageId);
        $chatMessage->delete();

        toast('Message deleted successfully' , 'success');
        return redirect()->back();
    }
}
