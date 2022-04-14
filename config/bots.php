<?php
return [
    'API'=>env('TELEGRAM_API_URL',''),
    'bot'=>[
        'username'=>"ShadowDamirBot",
        'token'=> "872163182:AAEgaCZGi9Xq8Ul0unyIzxDW03fi3AYiA10",
        'webHookURL' => "",
        'startMessage'=>'Hi $user',
        'commands'=>[
            \App\Telegram\Commands\StartCommand::class,
        ],
    ],
    'weather_api'=>env('WEATHER_API_URL',''),
    'weather_token'=>env('WEATHER_API_TOKEN','')
];
