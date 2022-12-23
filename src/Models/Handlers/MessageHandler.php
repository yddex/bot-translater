<?php
namespace Yddex\TranslateBot\Models\Handlers;
use Dejurin\GoogleTranslateForFree;
use Telegram\Bot\Objects\BaseObject;
use Telegram\Bot\Objects\Update;
use TelegramBot;
use Yddex\TranslateBot\Models\Chat;
use Yddex\TranslateBot\Models\LanguageKeyboard;
use Yddex\TranslateBot\Repositories\ChatRepository;

class MessageHandler extends Handler
{
    protected GoogleTranslateForFree $translater;

    public function __construct(TelegramBot $api, ChatRepository $chatRepository, GoogleTranslateForFree $translater)
    {
        parent::__construct($api, $chatRepository);
        $this->translater = $translater;
    }

    function handle(BaseObject $update) 
    {
        $chat = $this->getChat($update['message']['chat']);

        if(isset($update['message']['text']))
        {   
            //Получаем параметры для отправки
            $params = $this->getParams($update['message']['text'], $chat);

            $response = $this->api->sendMessage($params);
            return $response;
        }
        else //Если отправлен не текст, а файл, стикер и etc.
        {
            $response = $this->api->sendMessage([
                'chat_id' => $chat->getChatId(),
                'text' => 'Не могу это перевести :('
            ]);
            return $response;
        }

    }

    /**
     * 
     * @param  string Text from message of Telegram Update
     * @param  Chat Object chat
     * 
     * @return array Return params for send 
     */
    private function getParams(string $text, Chat $chat) :array
    {
        $startParams = [
            'chat_id' => $chat->getChatId(),
            'parse_mode' => 'html'
        ];

        switch($text)
        {
            case '/start':
                return array_merge($startParams, $this->startAnswer());
            case '/from':
                return array_merge($startParams, $this->fromAnswer($chat->getSourceLang()));
            case '/to': 
                return array_merge($startParams, $this->toAnswer($chat->getTargetLang()));
            default:
                return array_merge($startParams, $this->translateAnswer($text, $chat->getSourceLang(), $chat->getTargetLang()));
        }
    }


    //Ответ на команду /start
    private function startAnswer()
    {   
        $text = 'Напишите сообщение и бот отправит перевод.'. PHP_EOL .' Для перевода используется Google' . PHP_EOL .'Для настройки введите следующие команды:'. PHP_EOL.
        '/from - с какого языка переводить' . PHP_EOL . 
        '/to - на какой язык переводить' ;
        return [
            'text' => $text
        ];
    }

    //Команда /from
    private function fromAnswer(string $currentLang)
    {   
        $markup = [
            'inline_keyboard' => LanguageKeyboard::inlineFromKeyboard($currentLang)
        ];

        $text = 'С какого переводить?' . PHP_EOL . 'Выбранный язык: <b>' . LanguageKeyboard::getLanguageByCode($currentLang) . '</b>';
        return [
            'text' => $text,
            'reply_markup' => $this->api->replyKeyboardMarkup($markup)
        ];
    }

    //Команда /to
    private function toAnswer(string $currentLang)
    {
        $markup = [
            'inline_keyboard' => LanguageKeyboard::inlineToKeyboard($currentLang)
        ];
        $text = 'На какой переводить?' . PHP_EOL . 'Выбранный язык: <b>' . LanguageKeyboard::getLanguageByCode($currentLang) . '</b>';
        return [
            'text' => $text,
            'reply_markup' => $this->api->replyKeyboardMarkup($markup)
        ];
    }

    private function translateAnswer(string $text, string $source, string $target)
    {
        $translatedText = $this->translater->translate($source, $target, $text);
        return [
            'text' => $translatedText
        ];
    }
}