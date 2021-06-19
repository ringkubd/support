<?php

namespace App\Http\Controllers;

use App\Events\UserOfflineEvent;
use App\Models\User;
use Illuminate\Http\Request;

class UserOfflineController extends Controller
{
    public function __invoke(User $user)
    {
        $user->status = 0;
        $user->save();
        broadcast(new UserOfflineEvent($user));
    }

    public function updateStatus(User $user){
        $user->status = 0;
        $user->save();
        broadcast(new UserOfflineEvent($user));
        return $user;
    }
}
