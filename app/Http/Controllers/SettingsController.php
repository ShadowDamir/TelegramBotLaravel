<?php

namespace App\Http\Controllers;

use App\Facades\Telegram;
use App\Models\Distribution;
use App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function create() {
        $params = [];
        $settings = Settings::find(Auth::id())->first();
        $params = Arr::add($params,'token',$settings->token ?? '');
        $params = Arr::add($params,'username',$settings->username ?? '');
        $params = Arr::add($params,'webHookURL',$settings->webhookURL ?? '');
        $params = Arr::add($params,'startMessage',$settings->startMessage ?? '');
        return view('settings',compact('params'));
    }

    public function editParams(Request $request) {
        if(Str::length($request['token'])<=1){
            return back()->withErrors("Введен некорректный токен");
        }
        if(Str::length($request['username'])<=1){
            return back()->withErrors("Введен некорректное имя бота");
        }
        $settings = Settings::find(Auth::id())->first();
        $settings->token = $request['token'];
        $settings->username = $request['username'];
        if($request['welcomeMessage'] != null) {
            $settings->startMessage = $request['welcomeMessage'];
        }
        $settings->save();

        return back();
    }

    public function editWebHook(Request $request) {
        if($request['sumbitButton'] == 'change'){
            if($request['webhookURL'] == NULL) return back()->withErrors("Необходимо ввести значения вебхука");
            logger($request['webhookURL']);
            $result = Telegram::setWebHook($request['webhookURL']);
            if($result) {
                $settings = Settings::find(Auth::id())->first();
                $settings->webhookURL = $request['webhookURL'];
                $settings->save();
                return back();
            }
            return back()->withErrors("Не удалось установить вебхук");
        }
        if($request['sumbitButton'] == 'delete') {
            $result = Telegram::deleteWebHook();
            if($result) {
                $settings = Settings::find(Auth::id())->first();
                $settings->webhookURL = null;
                $settings->save();
                return back();
            }
            return back()->withErrors("Не удалось удалить вебхук");
        }
        return back();
    }
}
