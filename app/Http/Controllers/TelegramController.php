<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{Facades\Telegram,
    Models\Distribution,
    Models\File as FileModel,
    Models\Telegram_user,
    Telegram\Handlers\MyUpdateHandler};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class TelegramController extends Controller
{
    /**
     * function for start sending distributions
     */
    public static function Distribution() {
        $distributions = Distribution::where('isSended',0)->where('sendingDate','<=',now())->get();
        if(count($distributions)>0){
            $users = Telegram_user::where('isBanned', 0)->get();
            foreach ($distributions as $distribution){
                $fileId = -1;
                foreach ($users as $user){
                    if($distribution->image != null) {
                        $file = FileModel::find($distribution->image)->first();
                        $path = storage_path($file->file_path);
                        $result = Telegram::sendImage($user->userId, $file->file_path, $distribution->messageText);
                    }
                    else { Telegram::sendMessage($user->userId, $distribution->messageText); }
                }
                $distribution->isSended = true;
                $distribution->save();
            }
        }
    }


    /**
     * Changes bot config data or return exception message
     * @param Request $request request from form
     * @return mixed string/boolean
     */
    public function changeBotInfo(Request $request) {
        $result = Telegram_user::changeBotInfo($request);
        if($result['ok'] == false) return back()->withErrors('Не удалось сменить данные бота');
        return back();
    }

    /**
     * Get updates with long polling
     */
    public function getUpdates(){
        $result = Telegram::getUpdates();
        if(Arr::exists($result,'result') == false) return back();
        foreach ($result['result'] as $update){
            $this->handleUpdate(null,handledUpdate: $update);
        }
        return back();
    }

    /**
     * Set webhook for this bot
     */
    public function setWebHook(Request $request) {
        $result = Telegram::setWebHook($request['webhookURL']);
        if($result['ok'] == false) return back()->withErrors('Не удалось установить вебхук');
        $this->setMyCommands();
        return back();
    }

    /**
     * Remove webhook of this bot
     */
    public function deleteWebHook() {
        $result = Telegram::deleteWebHook();
        if($result['ok'] == false) return back()->withErrors('Не удалось удалить вебхук');
        return back();
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
        if($result['ok'] == false) return back()->withErrors('Не удалось установить комманды');
        return back();
    }

    /**
     * Delete commands descriptions of this bot
     */
    public function deleteMyCommands() {
        $result = Telegram::deleteCommands();
        if($result['ok'] == false) return back()->withErrors('Не удалось удалить комманды');
        return back();
    }

    /**
     * Handle updates - commands, messages and callbacks
     * @param Request $request request from telegram with update
     * @param null $handledUpdate update from getUpdates
     */
    public function handleUpdate(Request $request, $handledUpdate = null)
    {
        $update = $handledUpdate ?? $request;
        $user = Telegram_user::getOrCreate($update);
        if($user->isBanned) { return; }
        if (Arr::exists($update, 'message')) {
            if (Arr::exists($update['message'], 'entities')) MyUpdateHandler::handleCommand($update);
            else MyUpdateHandler::handleTextMessage($update);
        } else if (Arr::exists($update, 'inline_query')) MyUpdateHandler::handleInlineQuery($update);
        else if (Arr::exists($update, 'callback_query')) MyUpdateHandler::handleCallBackQuery($update);
    }
}
