<?php

namespace App\Models;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function messages(){
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }

    public function retrieveConversation($user2){
        $user1 = auth()->user()->id;
        $users = [(int)$user2, $user1];
        $conversation = $this->whereJsonContains('members', $users)->with('messages', 'user1', 'user2')->first();

        if (!$conversation) {
            $conversation = $this->create(['members' => json_encode($users), 'user1' => $user1, 'user2'=>$user2]);
        }
        return $conversation->toArray();
    }

    public function user1(){
        return $this->belongsTo(User::class, 'user1', 'id');
    }

    public function user2(){
        return $this->belongsTo(User::class, 'user2', 'id');
    }
}
