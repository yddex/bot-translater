<?php
use Dejurin\GoogleTranslateForFree;
use Symfony\Component\Dotenv\Dotenv;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Yddex\TranslateBot\Exceptions\UpdateException;
use Yddex\TranslateBot\Models\Handlers\CallbackHandler;
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

//telegram api 
$api = new TelegramBot($_ENV['BOT_TOKEN']);
//Chat repository
$chatRepository = new ChatRepository($connect);
//Translater
$translater = new GoogleTranslateForFree();
//Handlers
$messageHandler = new MessageHandler($api, $chatRepository, $translater);
$callbackHandler = new CallbackHandler($api, $chatRepository);

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

        //Ветвление для выбора обработчика
        if(isset($update[0]['message']))
        {
           $response =  $messageHandler->handle($update[0]);
        }
        else if(isset($update[0]['callback_query']))
        {
            try{
                $response = $callbackHandler->handle($update[0]);
            }
            catch( TelegramResponseException $e){
                print($e->getMessage() . PHP_EOL);
            }
            
        }

        $last_update = $update[0]['update_id'] + 1;
    }
    sleep(3);

}