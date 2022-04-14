<?php

namespace App\Http\Controllers;

use App\Models\Telegram_user;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UsersController extends Controller
{
    public function create() {
        $users = Telegram_user::all();
        return view('users.users',compact('users'));
    }

    public function createInfo($userId) {
        $user = Telegram_user::find($userId)->first();
        if($user != null) return view("users.userInfo",compact('user'));
        else return back();
    }

    public function banUser(Request $request){
        if(Arr::exists($request,'userId')) {
            $user = Telegram_user::find($request['userId']);
            $result = Telegram_user::banSwitch($user);
            return $this->createInfo($request['userId']);
        }
        else return $this->create();
    }
}
