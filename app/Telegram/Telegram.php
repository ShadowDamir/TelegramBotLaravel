<?php


namespace App\Telegram;

use App\Models\Settings;
use App\Telegram\Handlers\MyUpdateHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Telegram
{
    /**
     * @var mixed URL of Telegram Bot API
     */
    protected $API_URL;

    /**
     * @var HTTP
     */
    protected $http;

    /**
     * Telegram constructor.
     * @param $http
     */
    public function __construct($http)
    {
        $this->API_URL = config('bots.API','');
        $this->http=$http;
    }

    /*public function changeBotInfo($token,$username) {
        try{

            if(Arr::exists($request,'token')) {
                Config::set('bots.bot.token',$request['token']);
            }
            $this->bot = Config::get('bots.bot');
            if(Arr::exists($request,'webHookURL')) {
                if($this->setWebHook(Config::get('bots.bot.webHookURL'))['ok'] == false) {
                    throw new \Exception('Не удалось обновить вебхук.');
                }
                else {
                    Config::set('bots.bot.webHookURL',$request['webHookURL']);
                    $this->bot = Config::get('bots.bot');
                }
            }
            return true;
        }
        catch (\Exception $e){ return $e->getMessage(); }
    }*/

    /**
     * Get url for webhook
     * @return string
     */
    private function getUrl(){
        $settings = Settings::first();
        return $this->API_URL.$settings->token.'/';
    }

    /**
     * Set webhook on url
     * @param $url site_for_webhook
     * @return mixed
     */
    public function setWebHook($url, $controller = '/api/handleUpdate') {
        try{
            logger($url.$controller);
            $result = $this->http::post(self::getUrl().'setWebhook',[
                "url" => $url.$controller,
                "allowed_updates"=>["message","callback_query","inline_query"]
            ]);
            logger($result);
            return $result['ok'];
        }
        catch(\Exception) {
            return false;
        }
    }

    /**
     * Remove webhook
     * @return mixed
     */
    public function deleteWebHook(){
        $result = $this->http::get(self::getUrl().'deleteWebhook');
        if($result['ok']) return "{$result['result']} - {$result['description']}";
        else return "{$result['error_code']} - {$result['description']}";
    }

    /**
     * Get webhook information
     * @return mixed
     */
    public function getWebhookInfo(){
        return $this->http::get(self::getUrl().'getWebhookInfo')['result'];
    }

    /**
     * Getting updates with long polling. Works only if webhook was removed.
     * @return \Illuminate\Http\Client\Response
     */
    public function getUpdates(){
        $result = $this->http::get(self::getUrl().'getUpdates');
        return $result;
    }

    /**
     * Send message to chat
     * @param $chatId chatId
     * @param $message messageText
     * @param null $keyboard keyboard (replymarkupkeyboard or inlinekeyboard)
     * @return mixed
     */
    public function sendMessage($chatId, $message, $keyboard = null) {
        $params = [ 'chat_id'=>$chatId, 'text'=>$message, 'parse_mode'=> 'HTML' ];
        if($keyboard != null) $params = Arr::add($params,'reply_markup',$keyboard);
        $result = $this->http::post(self::getUrl().'sendMessage',$params);
        return $result;
    }

    /**
     * Answer on callback
     * @param $id CallbackId
     * @param int $cache_time
     * @return mixed
     */
    public function answerCallbackQuery($id,$cache_time = 1){
        $result = $this->http::get(self::getUrl().'answerCallbackQuery',[
            'callback_query_id'=>$id,
            'cache_time'=>$cache_time,
        ]);
        return $result;
    }

    /**
     * Send message with image and text
     * @param $chatId
     * @param $image
     * @param null $message
     * @return mixed
     */
    public function sendImage($chatId, $image, $message = null){
        $name = basename(Storage::path($image));

        $photo = fopen(Storage::path($image),'r');
        $params = [
            'chat_id'=>$chatId,
            'caption'=>$message,
            'parse_mode'=>'HTML'
        ];

        $result = $this->http::attach('photo',$photo,$name)
            ->post(self::getUrl().'sendPhoto',$params);

        if($result['ok']) {
            return Arr::get($result,'photo.0.file_id');
        }
        else {
            return "{$result['error_code']} - {$result['description']}";
        }
    }

    /**
     * Set commands for this bot
     * @return mixed
     */
    public function setCommands(){
        return $this->http::post(self::getUrl().'setMyCommands',[
            'commands' => [
                ['command'=>'/start', 'description'=>'Начать общение с ботом']
            ]
        ])['result'];
    }

    /**
     * Delete commands of this bot
     */
    public function deleteCommands(){
        return $this->http::get(self::getUrl().'deleteMyCommands')['result'];
    }

    /**
     * Get bot info
     */
    public function getMe() { return dd($this->http::get(self::getUrl().'getMe',)['result']); }
}
