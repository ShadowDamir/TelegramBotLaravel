<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Telegram_user extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'userId';

    protected $fillable = [
        'userId',
        'first_name',
        'last_name',
        'username',
        'customUsername',
        'requestName',
        'created_at',
        'updated_at',
        'is_bot',
        'isBanned',
    ];

    /**
     * Get Telegram_user or his id
     * @param $update - integer or Update
     * @return mixed
     */
    public static function getUser($update){
        $user = null;
        if (Arr::exists($update, 'message')) $user = Arr::get($update,'message.from');
        else if (Arr::exists($update, 'inline_query')) $user = Arr::get($update,'inline_query.from');
        else if (Arr::exists($update, 'callback_query')) $user = Arr::get($update,'callback_query.from');;
        return $user;
    }

    public static function banSwitch(Telegram_user $user){
         $user->isBanned = !($user->isBanned);
         $user->save();
    }

    public static function getOrCreate($update){
        $real_user = self::getUser($update);
        $user = self::firstOrCreate(
            ['userId' => $real_user['id']],
            [
                'first_name'=>$real_user['first_name'],
                'last_name'=>Arr::exists($real_user,'last_name') ? $real_user['last_name'] : null,
                'username'=>Arr::exists($real_user,'username') ? $real_user['username'] : null,
                'customUsername' => null,
                'requestName' => null,
                'is_bot' => 0,
                'isBanned'=> 0]
        );
        if($user->first_name != $real_user['first_name']) $user->first_name = $real_user['first_name'];
        if(Arr::exists($real_user,'last_name') && $user->last_name != $real_user['last_name']) $user->last_name = $real_user['last_name'];
        if(Arr::exists($real_user,'username') && $user->username != $real_user['username']) $user->username = $real_user['username'];
        return $user;
    }

    public static function changeName(Telegram_user $user, $name){
        $name = Str::words($name,1);
        $name = Str::of($name)->trim();
        $user->customUsername = $name;
        $user->save();
    }

    public static function ChangeRequest(Telegram_user $user, $request){
        $user->requestName = $request;
        $user->save();
    }
}
