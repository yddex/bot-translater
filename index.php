<?php
use Dejurin\GoogleTranslateForFree;
use Symfony\Component\Dotenv\Dotenv;
use Telegram\Bot\Api;
use PDO;
use Yddex\TranslateBot\Exceptions\UpdateException;
use Yddex\TranslateBot\Models\Handlers\MessageHandler;
use Yddex\TranslateBot\Models\Update;
use Yddex\TranslateBot\Repositories\ChatRepository;

//Для long polling
set_time_limit(0);
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/TelegramBot.php');



//Конфигурация
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

//Коннект к базе
$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'];
$connect = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);

//Требуемые обьекты
$api = new TelegramBot($_ENV['BOT_TOKEN']);
$chatRepository = new ChatRepository($connect);
$translater = new GoogleTranslateForFree();
$messageHundler = new MessageHandler($api, $chatRepository, $translater);

//START
//long polling
while (true) {
    if (isset($last_update)) {
        $update = $api->getUpdates(['offset' => $last_update]);
    } else {
        $update = $api->getUpdates();
    }

    print_r($update);
    if (count($update) !== 0) {
        file_put_contents(__DIR__ . '/logs/updates_log.txt', print_r($update, 1), FILE_APPEND);

        if(isset($update[0]['message']))
        {
           $response =  $messageHundler->handle($update[0]);
        }

        $last_update = $update[0]['update_id'] + 1;
    }
    sleep(5);

}