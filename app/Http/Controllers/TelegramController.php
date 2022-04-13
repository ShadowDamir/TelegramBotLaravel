<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Facades\Telegram,
    Models\Distribution,
    Models\File,
    Models\Telegram_user,
    Telegram\Handlers\MyUpdateHandler,
    Telegram\Commands as Commands};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    public static function Distribution() {
        $distributions = Distribution::where('isSended',0)->where('sendingDate','<=',now())->get();
        if(count($distributions)>0){
            $users = Telegram_user::where('isBanned', 0)->get();
            logger('users - '.$users);
            foreach ($distributions as $distribution){
                $fileId = -1;
                foreach ($users as $user){
                    if($distribution->image != null) {
                        $file = File::find($distribution->image)->first();
                        $path = storage_path($file->file_path);
                        logger($file->file_path);

                        //TODO: Сделать получние файла по пути к нему (пока не получается)
                        $file = Storage::get($file->file_path);
                        logger('file2 - '.$file);
                        $result = Telegram::sendImage($user->userId, $file, $distribution->messageText);
                        //if($result && $fileId = -1) $fileId = $result....
                    }
                    else {
                        logger('message - '.$distribution);
                        if(Telegram::sendMessage($user->userId, $distribution->messageText)){
                            $distribution->isSended = true;
                            $distribution->save();
                        }
                    }
                }
            }
        }
    }


    /**
     * Changes bot config data or return exception message
     * @param Request $request request from form
     * @return mixed string/boolean
     */
    public function changeBotInfo(Request $request) {
        return Telegram_user::changeBotInfo($request);
    }

    /**
     * Get updates with long polling
     */
    public function getUpdates(){
        $result = Telegram::getUpdates();
        foreach ($result as $update){
            $this->handleUpdate(handledUpdate: $update);
        }
        return back();
    }

    /**
     * Set webhook for this bot
     */
    public function setWebHook() {
        $result = Telegram::setWebHook(Config::get('bots.bot.webHookURL'));
        dd($result);
    }

    /**
     * Remove webhook of this bot
     */
    public function deleteWebHook() {
        dd(Telegram::deleteWebHook());
    }

    /**
     * Get info about installed webhook
     */
    public function getWebhookInfo() {
        $result = Telegram::getWebhookInfo();
        dd($result);
    }

    /**
     * Set commands description for this bot
     */
    public function setMyCommands() {
        $result = Telegram::setCommands();
        dd($result);
    }

    /**
     * Delete commands descriptions of this bot
     */
    public function deleteMyCommands() {
        $result = Telegram::deleteCommands();
        dd($result);
    }

    /**
     * Handle updates - commands, messages and callbacks
     */
    public function handleUpdate(Request $request = null, $handledUpdate = null)
    {
        $update = $request?? $handledUpdate;
        $user = Telegram_user::getOrCreate($update);
        if($user->isBanned) { return; }
        if (Arr::exists($update, 'message')) {
            if (Arr::exists($update['message'], 'entities')) MyUpdateHandler::handleCommand($update);
            else MyUpdateHandler::handleTextMessage($update);
        } else if (Arr::exists($update, 'inline_query')) MyUpdateHandler::handleInlineQuery($update);
        else if (Arr::exists($update, 'callback_query')) MyUpdateHandler::handleCallBackQuery($update);
    }
}
