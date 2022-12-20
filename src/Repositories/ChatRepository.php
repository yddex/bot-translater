<?php
namespace Yddex\TranslateBot\Repositories;


use DateTimeImmutable;
use PDO;
use Yddex\TranslateBot\Models\Chat;

class ChatRepository
{
    protected PDO $connect;
    public function __construct(PDO $connect)
    {
        $this->connect = $connect;
    }

    public function register(Chat $chat)
    {
        $query = "INSERT INTO chats (chat_id, first_name, username, source_lang, target_lang, register_at)
                VALUES (:chat_id, :first_name, :username, :source_lang, :target_lang, :register_at)";
        $stmt = $this->connect->prepare($query);
        $stmt->execute([
            'chat_id' => $chat->getChatId(),
            'first_name' => $chat->getFirstName(),
            'username' => $chat->getUsername(),
            'source_lang' => $chat->getSourceLang(),
            'target_lang' => $chat->getTargetLang(),
            'register_at' => (new DateTimeImmutable())->format(DATE_ATOM)
        ]);
    }

    public function updateLang(Chat $chat)
    {
        $query = "UPDATE chats SET source_lang = :source_lang, target_lang = :target_lang WHERE chat_id = :chat_id";
        $stmt = $this->connect->prepare($query);
        $stmt->execute([
            'source_lang' => $chat->getSourceLang(),
            'target_lang' => $chat->getTargetLang(),
            'chat_id' => $chat->getChatId()
        ]);
    }

    public function getChat(int $chat_id) :Chat | false
    {
        $query = "SELECT * FROM chats WHERE chat_id = ?";
        $stmt = $this->connect->prepare($query);
        $stmt->execute([
            $chat_id
        ]);
        $result = $stmt->fetch();
        return $result === false ? false : $this->createChatOfStmt($result);
    
    }


    protected function createChatOfStmt(array $result) :Chat
    {
        $chat_id = $result['chat_id'];
        $first_name = $result['first_name'];
        $username = $result['username'];
        $source_lang = $result['source_lang'];
        $target_lang = $result['target_lang'];

        return new Chat($chat_id, $first_name, $username, $source_lang, $target_lang);
    }

}