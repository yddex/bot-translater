<?php
set_time_limit(0);
require_once(__DIR__ . '/vendor/autoload.php');
use Telegram\Bot\Api;

const TOKEN = '5746875700:AAGGKwBo4OYM47GjRGuh5WWvN1hHGki3xrk';
const API = 'https://api.telegram.org/bot' . TOKEN;

// function sendMessage($chat_id, $message, $mute=false, $pmode='HTML', $replyMarkup=false){
//     $url = API . "/sendMessage";
//     $post_fields = array(
//         'chat_id'   => $chat_id,
//         'text'      => $message,
//         'disable_notification' => $mute,
//         'parse_mode'=> $pmode,
//         'reply_markup'=>$replyMarkup
//     );
//     $ch = curl_init(); 
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Content-Type:multipart/form-data" ));
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
//     $response = curl_exec($ch);
//     curl_close($ch);
//     usleep(500);
//     $res = json_decode($response, TRUE);
//     return $res;
// }




$api = new Api(TOKEN);
while (true) {
    if (isset($last_update)) {
        $update = $api->getUpdates(['offset' => $last_update]);
    } else {
        $update = $api->getUpdates();
    }

    print_r($update);
    if ($update) {
        $update = $update[0];
        file_put_contents(__DIR__ . '/logs/updates_log.txt', print_r($update, 1), FILE_APPEND);

        if ($update['message']['text'] == '/start') {
            $chat_id = $update['message']['chat']['id'];
            $setting_keyboard = [
                [
                    ['text' => "Russian -> English", 'callback_data' => 'ru:en'],
                    ['text' => "English -> Russian", 'callback_data' => 'en:ru'],
                ]
            ];
            $text = "Бот переводчик переведет ваши сообщения и отправит их вам." . PHP_EOL .
                "Для начала выберите языки перевода, введя <i>/setting</i>";
            $params = [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'html'
            ];
            $api->sendMessage($params);


        } else if ($update['message']['text'] == '/setting') {
            $chat_id = $update['message']['chat']['id'];
            $setting_keyboard = [
                [
                    ['text' => "Russian", 'callback_data' => 'ru'],
                    ['text' => "English", 'callback_data' => 'en'],
                ]
            ];
            $text = "С какого языка будем переводить?";
            $params = [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'html',
                'reply_markup' => $api->replyKeyboardMarkup(['inline_keyboard' => $setting_keyboard])
            ];
            $response = $api->sendMessage($params);
            file_put_contents(__DIR__ . '/logs/response_log.txt', print_r($response, 1), FILE_APPEND);
        }
        $last_update = $update['update_id'] + 1;
    }
    sleep(5);

}