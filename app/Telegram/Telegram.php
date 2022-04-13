<?php


namespace App\Telegram;

use App\Telegram\Handlers\MyUpdateHandler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class Telegram
{
    /**
     * @var mixed URL of Telegram Bot API
     */
    protected $API_URL;

    /**
     * @var Current_HTTP
     */
    protected $http;
    /**
     * @var Current_bot
     */
    protected $bot;

    /**
     * Telegram constructor.
     * @param $http
     * @param $bot
     */
    public function __construct($http, $bot)
    {
        $this->API_URL = env('TELEGRAM_API_URL','');
        $this->http=$http;
        $this->bot=$bot;
    }

    public function changeBotInfo($request) {
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
    }

    /**
     * Get url for webhook
     * @return string
     */
    private function getUrl(){
        return $this->API_URL.$this->bot['token'].'/';
    }

    /**
     * Set webhook on url
     * @param $url site_for_webhook
     * @return mixed
     */
    public function setWebHook($url, $controller = '/api/handleUpdate') {
        $result = $this->http::post(self::getUrl().'setWebhook',[
            "url" => $url.$controller,
            "allowed_updates"=>["message","callback_query","inline_query"]
        ]);
        logger($result);
        logger($url.$controller);
        if($result['ok']) return "{$result['result']} - {$result['description']}";
        else return "{$result['error_code']} - {$result['description']}";
        return $result;

    }

    /**
     * Remove webhook
     * @return mixed
     */
    public function deleteWebHook(){
        $result = $this->http::get(self::getUrl().'deleteWebhook');
        logger($result);
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
     * @return array
     */
    public function getUpdates(){
        $result = $this->http::get(self::getUrl().'getUpdates');
        return $result['result'];
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
        logger($keyboard);
        if($keyboard != null) $params = Arr::add($params,'reply_markup',$keyboard);
        $result = $this->http::post(self::getUrl().'sendMessage',$params);
        logger($result);
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
        logger($result);
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
        logger($image);
        $params = [ 'chat_id'=>$chatId, 'photo'=>$image, ];
        if($message != null) {
            $params = Arr::add($params,'caption',$message);
            $params = Arr::add($params,'parse_mode','HTML');
            logger($params);
        }
        logger($params);
        $result = $this->http::post(self::getUrl().'sendPhoto',$params);
        logger($result);
        if($result['ok']) return "{$result['result']} - {$result['description']}";
        else return "{$result['error_code']} - {$result['description']}";
        //TODO: Сделать корректный выбор файла
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
