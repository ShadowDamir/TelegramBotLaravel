<?php
namespace App\Telegram\Commands;

use App\Facades\Telegram;
use App\Models\Telegram_user;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertIsBool;

class GetMeCommand
{
    public static function handle($update)
    {
        $user = Telegram_user::getOrCreate($update);
        $isBot = $user->is_bot ? "true" : "false";;
        $isBanned = $user->isBanned ? "true" : "false";
        $username = $user->customUsername ?? 'не установлено';
        $text = "<u><b>UserId</b></u> - {$user->userId}\n"
                ."<u><b>First name</b></u> - {$user->first_name}\n"
                ."<u><b>Second name</b></u> - {$user->second_name}\n"
                ."<u><b>CustomUsername</b></u> - $username\n"
                ."<u><b>Registration date</b></u> - {$user->created_at}\n"
                ."<u><b>Bot status</b></u> - $isBot\n"
                ."<u><b>Ban status</b></u> - $isBanned";
        Telegram::sendMessage($user->userId, $text);
    }
}
