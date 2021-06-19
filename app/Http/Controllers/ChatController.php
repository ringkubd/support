<?php

namespace App\Http\Controllers;

use App\Events\MessageReceiveEvent;
use App\Events\MessagingEvent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $friends = User::where('id', '!=', auth()->user()->id)->get();
        return Inertia::render('Chat/inbox', [
            "friends" => $friends
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => ['required', 'integer'],
            'body' => ['required'],
            'from_user' => ['required'],
            'to_user' => ['required'],
        ]);

        $conversation = Conversation::find($request->conversation_id);
        $conversation->messages()->create($request->all());
        //broadcast(new MessageReceiveEvent(auth()->user(), $conversation));
        broadcast(new MessagingEvent(auth()->user(), $conversation));
        return $conversation;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getConversation($userid){
        $conversation = new Conversation();
        return $conversation->retrieveConversation($userid);
    }
}
