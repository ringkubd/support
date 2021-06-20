<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('chat', function ($user){
    return $user;
});

Broadcast::channel('messages.{conversation}', function ($user, Conversation $conversation){
    $conversation_user = $conversation->members;
    if (in_array($user->id, $conversation_user)){
        return true;
    }
    return false;
});

Broadcast::channel('messaging.{conversation}', function ($user, Conversation $conversation){
    $conversation_user = json_decode($conversation->members);
    if (in_array($user->id, $conversation_user)){
        return true;
    }
    return false;
});
