<?php
namespace App\Telegram\Commands;

use App\Facades\Telegram;
use App\Models\Settings;
use App\Models\Telegram_user;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StartCommand
{
    public static function handle($update)
    {
        $user = Telegram_user::getOrCreate($update);
        $username = $user->customUsername ?? Telegram_user::getUser($update)['first_name'];
        $keyboard = [
            [["text" => "Ввести имя"], ["text" => "Список пользователей"]],
            [["text" => "Текущая температура воздуха МСК"]]
        ];
        $replyKeyboard = [
            'keyboard' => $keyboard,
            'resize_keyboard' => true];
        $settings = Settings::first();
        $text = $settings->startMessage ?? "Приветствую вас, $username!\nМои функции представлены на клавиатуре ниже.";
        $result = Telegram::sendMessage($user->userId, $text, $replyKeyboard);
    }
}
