<?php
namespace Yddex\TranslateBot\Models;
use DateTimeImmutable;

class Chat
{
    protected int $chat_id;
    protected string $first_name;
    protected ?string $username;
    protected string $source_lang;
    protected string $target_lang;
   
    public function __construct(
        int $chat_id,
        string $first_name,
        ?string $username,
        string $source_lang ='ru',
        string $target_lang = 'en',
    )
    {
        $this->chat_id = $chat_id;
        $this->first_name = $first_name;
        $this->username = $username;
        $this->source_lang = $source_lang;
        $this->target_lang = $target_lang;
    }


    //GETTERS
    public function getChatId()
    {
            return $this->chat_id;
    }

    public function getFirstName()
    {
            return $this->first_name;
    }


    public function getUsername()
    {
            return $this->username;
    }


    public function getSourceLang()
    {
            return $this->source_lang;
    }

    
    public function getTargetLang()
    {
            return $this->target_lang;
    }


    public function setSourceLang(string $source_lang): void
    {
        $this->source_lang = $source_lang;
    }


    public function setTargetLang(string $target_lang): void
    {
        $this->target_lang = $target_lang;
    }
}

