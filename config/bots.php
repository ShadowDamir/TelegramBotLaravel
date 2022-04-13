<?php
return [
    'bot'=>[
        'username'=>"",
        'token'=> "",
        'webHookURL' => "",
        'commands'=>[
            \App\Telegram\Commands\StartCommand::class,
        ]
    ]
];
