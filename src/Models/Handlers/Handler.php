<?php
namespace Yddex\TranslateBot\Models\Handlers;

use Telegram\Bot\Objects\BaseObject;
use Telegram\Bot\TelegramResponse;
use TelegramBot;
use Telegram\Bot\Objects\Update;
use Yddex\TranslateBot\Models\Chat;
use Telegram\Bot\Objects\CallbackQuery;
use Yddex\TranslateBot\Repositories\ChatRepository;

abstract class Handler
{

    protected TelegramBot $api;
    protected ChatRepository $chatRepository;
   
    public function __construct(TelegramBot $api, ChatRepository $chatRepository)
    {
        $this->api = $api;
        $this->chatRepository = $chatRepository;
    }

    /**
     * Summary of getChat
     * @param \Telegram\Bot\Objects\Chat $chatData
     * @return Chat
     */
    protected function getChat(\Telegram\Bot\Objects\Chat $chatData) :Chat
    {
        $chat_id = $chatData['id'];
        if($chat = $this->chatRepository->getChat($chat_id)){

            return $chat;

        }else{
            $first_name = $chatData['first_name'];
            $username = $chatData['username'] ?? null;

            $chat = new Chat($chat_id, $first_name, $username);
            $this->chatRepository->register($chat);

            return $chat;
        }
       
    }
    abstract function handle(BaseObject $update);
}