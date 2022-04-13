<?php


namespace App\Telegram\Handlers;
use App\Facades\Telegram;
use App\Http\Controllers\WeatherController;
use App\Models\Telegram_user;
use App\Models\User;
use App\Telegram\Commands\{GetMeCommand, StartCommand};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MyUpdateHandler
{
    public static function handleCommand($update){
        $user = Telegram_user::getOrCreate($update);
        if($user->requestName == 'name') Telegram_user::ChangeRequest($user, null);
        switch ($update['message']['text']){
            case '/start':
                StartCommand::handle($update);
                break;
            case '/getMe':
                GetMeCommand::handle($update);
                break;
            default:
                $userId = Telegram_user::getOrCreate($update)['userId'];
                Telegram::sendMessage($userId,"Я не понимаю такую команду");
                break;
        }
    }

    /**
     * Logic of handling messages from updates
     * @param $update - update from telegram
     */
    public static function handleTextMessage($update)
    {
        $user = Telegram_user::getOrCreate($update);
        $username = $user->customUsername ?? Telegram_user::getUser($update)['first_name'];
        switch (Arr::get($update,'message.text')) {
            case 'Ввести имя':
                if($user->customUsername != null) {
                    Telegram::sendMessage($user->userId,"Я уже знаю как вас зовут, {$user->customUsername}.");
                    break;
                }
                Telegram_user::ChangeRequest($user,'name');
                Telegram::sendMessage($user->userId,"Введите ваше имя в следующем сообщении.");
                break;
            case 'Список пользователей':
                if($user->requestName == 'name') Telegram_user::ChangeRequest($user, null);
                $users = Telegram_user::all();
                $list = [];
                for ($i = 0; $i<$users->count(); $i++) {
                    $telegram_user = $users[$i];
                    $name = $telegram_user->customUsername ?? $telegram_user->first_name;
                    $button = [ ['text'=>$name, 'callback_data'=>"{$telegram_user->userId}|{$name}"] ];
                    $list = Arr::add($list,$i,$button);
                }
                $inlineKeyboard = [ 'inline_keyboard' => $list, 'resize_keyboard' => true];

                Telegram::sendMessage($user->userId,"Список пользователей:",$inlineKeyboard);
                break;
            case 'Текущая температура воздуха МСК':
                try{
                    $result = WeatherController::getWeatherInCity("Moscow");
                    logger($result);
                    $temp = Round(Arr::get($result,"main.temp")); $temp = $temp > 0 ? '+'.$temp : $temp;
                    $feelsTemp = Round(Arr::get($result,"main.feels_like")); $feelsTemp = $feelsTemp > 0 ? '+'.$feelsTemp : $feelsTemp;
                    Telegram::sendMessage($user->userId,"Температура в градусах по Цельсия в <b>Москве</b> - <b>{$temp}°</b>.\n"
                                                       ."Ощущается как - <b>{$feelsTemp}°</b>.");
                }
                catch (\Exception) {
                    Telegram::sendMessage($user->userId,"Не удалось получить узнать в Москве.");
                }
                break;
            default:
                if($user->requestName == 'name') {
                    Telegram_user::changeName($user, Arr::get($update,'message.text'));
                    Telegram_user::ChangeRequest($user, null);
                    Telegram::sendMessage($user->userId,"Теперь я буду называть вас \"{$user->customUsername}\"!");
                }
                else Telegram::sendMessage($user->userId,"Я вас не понимаю, $username :(");
                break;
        }
    }

    /**
     * Logic of handling inlineQueries updates
     * @param $update - update from telegram
     */
    public static function handleInlineQuery($update)
    {
        $user = Telegram_user::getOrCreate($update);
        if($user->requestName == 'name') Telegram_user::ChangeRequest($user, null);
        // Here you can write your own logic of handling this updates
    }

    /**
     * Logic of handling callBackQueries updates
     * @param $update - update from telegram
     */
    public static function handleCallBackQuery($update)
    {
        $user = Telegram_user::getOrCreate($update);
        if($user->requestName == 'name') Telegram_user::ChangeRequest($user, null);
        $callBack = Arr::get($update,'callback_query');

        $userId = Str::of($callBack['data'])->before('|');
        $username = Str::of($callBack['data'])->after('|');
        $date = Telegram_user::find($userId)->first()->created_at;
        logger("$userId | $username");

        $text = "<u><b>$username</b></u> первый раз взаимодействовал со мной <u><b>{$date->format('d.m.Y')}</b></u>";
        Telegram::sendMessage($user->userId, $text);
        Telegram::answerCallbackQuery($callBack['id']);

    }
}
