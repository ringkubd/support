<?php

namespace App\Http\Controllers;

use App\Events\UserOnline;
use App\Models\User;
use Illuminate\Http\Request;

class UsersOnlineController extends Controller
{
    public function __invoke(User $user){
        $user->status = 1;
        $user->save();
        broadcast(new UserOnline($user));
    }

    public function updateStatus(User $user){
        $user->status = 1;
        $user->save();
        broadcast(new UserOnline($user));
    }
}
